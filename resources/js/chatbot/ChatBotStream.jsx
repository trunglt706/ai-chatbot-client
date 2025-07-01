import React, { useState, useRef, useEffect } from "react";
import AudioRecorderModal from "./AudioRecorderModal";
import "../../css/chatbot/ChatBotStream.css";

// Số tin nhắn mỗi trang khi tải lịch sử
const MESSAGES_PER_PAGE = 10;
const LINK_SEND_MESSAGE = "/chatbot/send";
const LINK_GET_HISTORY = "/chatbot/history";
const LINK_SPEECH_TO_TEXT = "/chatbot/speech-to-text";

export default function ChatBotStream() {
    const [messages, setMessages] = useState([]);
    const [input, setInput] = useState("");
    const [botTyping, setBotTyping] = useState(false);
    const [showRecorder, setShowRecorder] = useState(false);
    const [audioLoading, setAudioLoading] = useState(false);
    const [historyLoading, setHistoryLoading] = useState(true); // Trạng thái cho lần tải lịch sử ban đầu
    const [currentPage, setCurrentPage] = useState(1); // Trang lịch sử hiện tại đang được tải
    const [hasMoreHistory, setHasMoreHistory] = useState(true); // Cờ báo hiệu còn lịch sử cũ hơn hay không
    const [isLoadingOldMessages, setIsLoadingOldMessages] = useState(false); // Trạng thái khi đang tải tin nhắn cũ hơn do cuộn

    const endRef = useRef(null); // Ref để cuộn xuống cuối cuộc trò chuyện
    const chatBodyRef = useRef(null); // Ref cho phần thân cuộc trò chuyện để theo dõi cuộn
    const scrollObserverRef = useRef(null); // Ref cho Intersection Observer

    const suggestedQuestions = [
        "Bạn có thể giúp gì cho tôi?",
        "Làm sao để đăng ký tài khoản?",
        "Tính năng của hệ thống là gì?",
        "Giờ làm việc của bạn là khi nào?",
        "Tôi cần liên hệ với ai để được hỗ trợ?",
    ];

    // Hàm trợ giúp để thêm tin nhắn hệ thống
    const addSystemMessage = (text) => {
        setMessages((prev) => [...prev, { sender: "system", text }]);
    };

    /**
     * Hàm chính để tải lịch sử trò chuyện.
     * Được định nghĩa ngoài useEffect để có thể truy cập state mới nhất.
     * @param {number} pageToLoad Trang lịch sử cần tải.
     */
    const loadChatHistory = async (pageToLoad) => {
        // Đặt cờ tải tùy thuộc vào việc đây là lần tải ban đầu hay tải thêm do cuộn
        if (pageToLoad === 1) {
            setHistoryLoading(true);
        } else {
            setIsLoadingOldMessages(true);
        }

        // Lưu lại chiều cao cuộn trước khi thêm tin nhắn mới (chỉ áp dụng khi tải tin nhắn cũ hơn)
        const prevScrollHeight = chatBodyRef.current
            ? chatBodyRef.current.scrollHeight
            : 0;

        try {
            const response = await fetch(
                LINK_GET_HISTORY +
                    `?page=${pageToLoad}&limit=${MESSAGES_PER_PAGE}`,
                {
                    method: "GET",
                    headers: {
                        Accept: "text/event-stream", // Chấp nhận text/event-stream
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                }
            );

            // Xử lý lỗi HTTP status (ví dụ: 401 Unauthorized, 400 Bad Request, v.v.)
            if (!response.ok) {
                let errorText = response.statusText;
                try {
                    // Cố gắng đọc body nếu có JSON lỗi
                    const errorData = await response.json();
                    errorText =
                        errorData.error || errorData.message || errorText;
                } catch (e) {
                    // Bỏ qua lỗi parse nếu response không phải JSON
                }
                console.error("Failed to load chat history:", errorText);
                addSystemMessage(
                    `⚠️ Không thể tải lịch sử trò chuyện. Lỗi: ${errorText}`
                );
                setHasMoreHistory(false); // Đặt cờ hết lịch sử nếu có lỗi
                // Nếu đây là lần tải ban đầu và không có tin nhắn nào, gửi tin chào mừng
                if (pageToLoad === 1 && messages.length === 0) {
                    sendMessage("");
                }
                return;
            }

            const reader = response.body.getReader();
            const decoder = new TextDecoder("utf-8");
            let accumulatedMessages = []; // Mảng tạm để tích lũy tin nhắn của trang này
            let doneReadingStream = false;
            let receivedHasMore = false; // Biến cờ để lưu trữ giá trị hasMore từ sự kiện DONE

            while (true) {
                const { value, done } = await reader.read();
                if (done) {
                    doneReadingStream = true;
                    break;
                }

                const chunk = decoder.decode(value, { stream: true });
                const lines = chunk.split("\n");

                for (const line of lines) {
                    if (line.startsWith("data: ")) {
                        const content = line.replace("data: ", "");
                        try {
                            const parsedContent = JSON.parse(content);
                            if (parsedContent.done) {
                                // Đây là tín hiệu [DONE] từ backend, chứa thông tin hasMore
                                receivedHasMore = parsedContent.hasMore;
                                doneReadingStream = true; // Báo hiệu đã đọc xong luồng
                                break; // Thoát vòng lặp nội bộ
                            } else {
                                // Tin nhắn lịch sử thông thường
                                accumulatedMessages.push({
                                    sender: parsedContent.sender,
                                    text: parsedContent.text,
                                });
                            }
                        } catch (parseError) {
                            console.error(
                                "Lỗi khi phân tích tin nhắn lịch sử:",
                                parseError,
                                "Nội dung:",
                                content
                            );
                        }
                    }
                }
                if (doneReadingStream) break; // Thoát vòng lặp chính nếu [DONE] đã được xử lý
            }

            // Cập nhật trạng thái tin nhắn bằng cách thêm tin nhắn mới vào ĐẦU mảng
            if (accumulatedMessages.length > 0) {
                setMessages((prev) => [...accumulatedMessages, ...prev]);
                // Nếu đây là lần tải thêm do cuộn (không phải tải ban đầu), điều chỉnh vị trí cuộn
                if (pageToLoad > 1 && chatBodyRef.current) {
                    const newScrollHeight = chatBodyRef.current.scrollHeight;
                    // Điều chỉnh scrollTop để giữ cho vị trí hiển thị hiện tại không bị nhảy
                    chatBodyRef.current.scrollTop =
                        newScrollHeight - prevScrollHeight;
                }
            }
            setHasMoreHistory(receivedHasMore); // Cập nhật cờ còn lịch sử hay không
            setCurrentPage(pageToLoad); // Cập nhật trang hiện tại

            // Sau khi tải lịch sử hoàn tất, nếu không có tin nhắn nào được tải
            // VÀ đây là lần tải ban đầu (pageToLoad === 1) VÀ không còn lịch sử cũ hơn (hết dữ liệu)
            // thì gửi tin nhắn chào mừng bot.
            if (
                pageToLoad === 1 &&
                accumulatedMessages.length === 0 &&
                !receivedHasMore
            ) {
                sendMessage("");
            }
        } catch (error) {
            console.error("Lỗi khi fetch lịch sử chat:", error);
            addSystemMessage("❌ Lỗi kết nối khi tải lịch sử trò chuyện.");
            setHasMoreHistory(false); // Đặt cờ hết lịch sử nếu có lỗi kết nối
            // Gửi tin nhắn chào mừng nếu là lỗi ở lần tải ban đầu
            if (pageToLoad === 1 && messages.length === 0) {
                sendMessage("");
            }
        } finally {
            if (pageToLoad === 1) setHistoryLoading(false);
            else setIsLoadingOldMessages(false);
        }
    };

    /**
     * Gửi tin nhắn văn bản đến chatbot và xử lý luồng phản hồi.
     * @param {string} message Tin nhắn văn bản từ người dùng.
     */
    const sendMessage = async (message) => {
        // Nếu tin nhắn rỗng hoặc chỉ chứa khoảng trắng, và đây không phải là tin nhắn khởi tạo ban đầu (messages.length === 0)
        // thì không gửi đi.
        if (!message.trim() && messages.length > 0) {
            return; // Dừng hàm nếu là tin nhắn rỗng không phải khởi tạo.
        }

        // Add user message if not empty (this ensures the last message is always 'user' before bot starts)
        if (message) {
            const userMsg = { sender: "user", text: message };
            setMessages((prev) => [...prev, userMsg]);
            setInput(""); // Xóa input sau khi gửi
        }

        setBotTyping(true); // Đặt trạng thái bot đang trả lời

        try {
            const response = await fetch(LINK_SEND_MESSAGE, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "text/event-stream",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
                body: JSON.stringify({ message }),
            });
            if (response.ok) {
                const reader = response.body.getReader();
                const decoder = new TextDecoder("utf-8");
                let botReply = "";

                while (true) {
                    const { value, done } = await reader.read();
                    if (done) break;

                    const chunk = decoder.decode(value, { stream: true });
                    const lines = chunk.split("\n");

                    for (const line of lines) {
                        if (line.startsWith("data: ")) {
                            const content = line.replace("data: ", "");
                            if (content === "[DONE]") {
                                setBotTyping(false); // Dừng trạng thái bot đang trả lời
                                return;
                            }

                            botReply += content;

                            // Cập nhật tin nhắn bot trong trạng thái
                            setMessages((prev) => {
                                const last = prev[prev.length - 1];
                                if (last?.sender === "bot") {
                                    return [
                                        ...prev.slice(0, -1),
                                        { sender: "bot", text: botReply },
                                    ];
                                } else {
                                    return [
                                        ...prev,
                                        { sender: "bot", text: botReply },
                                    ];
                                }
                            });
                        }
                    }
                }
            } else {
                setBotTyping(false); // Dừng trạng thái bot đang trả lời khi có lỗi
                addSystemMessage("❌ Gửi tin nhắn thất bại!");
            }
        } catch (error) {
            console.error("Lỗi khi gửi tin nhắn:", error);
            setBotTyping(false); // Dừng trạng thái bot đang trả lời khi có lỗi
            addSystemMessage(
                "❌ Xin lỗi, đã xảy ra lỗi. Vui lòng thử lại sau!"
            );
        }
    };

    /**
     * Xử lý file ghi âm nhận được từ AudioRecorderModal.
     * Gửi file đến backend để chuyển giọng nói thành văn bản.
     * @param {Blob} audioBlob Blob chứa dữ liệu âm thanh đã ghi.
     */
    const handleSendAudio = async (audioBlob) => {
        setAudioLoading(true); // Bắt đầu trạng thái xử lý audio
        addSystemMessage("🎙️ *Đang xử lý ghi âm...*"); // Hiển thị tin nhắn hệ thống

        const formData = new FormData();
        formData.append("audio", audioBlob, "recording.webm"); // Tên file nên có phần mở rộng phù hợp

        try {
            const response = await fetch(LINK_SPEECH_TO_TEXT, {
                method: "POST",
                headers: {
                    // 'Content-Type' không cần thiết khi dùng FormData, trình duyệt tự đặt
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
                body: formData, // Gửi FormData chứa file âm thanh
            });

            const result = await response.json(); // Phân tích phản hồi JSON từ backend
            setAudioLoading(false); // Kết thúc trạng thái xử lý audio

            if (response.ok) {
                // Kiểm tra nếu phản hồi thành công (status 2xx)
                if (result.text) {
                    // Backend trả về 'text' khi phiên âm thành công
                    // Gửi văn bản phiên âm nhận được từ backend làm tin nhắn của người dùng
                    sendMessage(result.text);
                } else if (result.message) {
                    // Backend trả về 'message' cho các trường hợp quota
                    sendMessage({ sender: "bot", text: result.message }); // Hiển thị thông báo quota
                } else {
                    // Trường hợp không có cả 'text' và 'message' khi thành công
                    addSystemMessage(
                        "⚠️ *Backend không trả về nội dung phiên âm.*"
                    );
                }
            } else {
                // Phản hồi không thành công (status 4xx, 5xx)
                if (result.error) {
                    // Backend trả về 'error' khi có lỗi
                    addSystemMessage(`❌ *Lỗi: ${result.error}*`);
                } else if (result.message) {
                    // Một số lỗi có thể trả về 'message'
                    addSystemMessage(`❌ *Lỗi: ${result.message}*`);
                } else {
                    addSystemMessage(
                        "❌ *Đã xảy ra lỗi không xác định khi xử lý ghi âm.*"
                    );
                }
            }
        } catch (error) {
            console.error("Lỗi gửi audio:", error);
            setAudioLoading(false); // Dừng trạng thái xử lý audio khi có lỗi
            addSystemMessage(
                "❌ *Lỗi kết nối hoặc xử lý ghi âm. Vui lòng thử lại.*"
            );
        } finally {
            setShowRecorder(false); // Luôn đóng modal ghi âm sau khi gửi, bất kể thành công hay thất bại
        }
    };

    // Cuộn xuống cuối tin nhắn khi có thay đổi trong messages, botTyping, audioLoading
    useEffect(() => {
        // Chỉ cuộn xuống nếu không đang tải lịch sử cũ hơn hoặc đây là lần tải ban đầu hoàn tất
        if (!isLoadingOldMessages) {
            endRef.current?.scrollIntoView({ behavior: "smooth" });
        }
    }, [messages, botTyping, audioLoading]); // historyLoading không cần ở đây vì nó ảnh hưởng đến trạng thái ban đầu

    // Effect để tải lịch sử trò chuyện khi component mount lần đầu tiên
    useEffect(() => {
        loadChatHistory(1);
    }, []); // [] đảm bảo effect chỉ chạy 1 lần khi mount

    // Effect để thiết lập Intersection Observer cho việc tải lịch sử cũ hơn
    useEffect(() => {
        if (!chatBodyRef.current) return;

        // Ngắt kết nối observer cũ nếu có
        if (scrollObserverRef.current) {
            scrollObserverRef.current.disconnect();
        }

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    // Nếu phần tử "sentinel" (ở đầu chat) đang hiển thị hoàn toàn
                    // VÀ còn lịch sử cũ hơn để tải
                    // VÀ không đang tải lịch sử cũ hơn
                    // VÀ không đang trong quá trình tải lịch sử ban đầu
                    if (
                        entry.isIntersecting &&
                        entry.intersectionRatio === 1 &&
                        hasMoreHistory &&
                        !isLoadingOldMessages &&
                        !historyLoading
                    ) {
                        console.log("Loading more history...");
                        // Gọi hàm loadChatHistory với trang tiếp theo
                        loadChatHistory(currentPage + 1);
                    }
                });
            },
            {
                root: chatBodyRef.current, // Quan sát bên trong phần thân chat
                threshold: 1.0, // Kích hoạt khi 100% của phần tử "sentinel" hiển thị
                rootMargin: "0px 0px 0px 0px", // Không có margin thêm
            }
        );

        // Tạo một phần tử "sentinel" nhỏ và vô hình ở đầu chat body
        // Phần tử này sẽ được observer theo dõi
        const sentinel = document.createElement("div");
        sentinel.style.height = "1px";
        sentinel.style.width = "100%";
        sentinel.style.position = "absolute"; // Đặt vị trí tuyệt đối để nó luôn ở đầu
        sentinel.style.top = "0";
        sentinel.style.left = "0";
        sentinel.style.pointerEvents = "none"; // Đảm bảo nó không chặn các sự kiện chuột
        // Thêm sentinel vào đầu chat body
        chatBodyRef.current.prepend(sentinel);

        // Bắt đầu quan sát phần tử sentinel
        observer.observe(sentinel);
        scrollObserverRef.current = observer; // Lưu observer vào ref

        // Hàm dọn dẹp khi component unmount hoặc dependencies thay đổi
        return () => {
            if (scrollObserverRef.current) {
                scrollObserverRef.current.disconnect();
            }
            // Xóa phần tử sentinel khi component unmount
            if (sentinel.parentNode) {
                sentinel.parentNode.removeChild(sentinel);
            }
        };
    }, [hasMoreHistory, isLoadingOldMessages, historyLoading, currentPage]); // Re-run effect nếu các state này thay đổi

    return (
        <div className="chat-container">
            <div className="suggestion-box">
                <h4 className="suggestion-title">💡 Gợi ý câu hỏi</h4>
                <ul className="suggestion-list">
                    {suggestedQuestions.map((question, idx) => (
                        <li
                            key={idx}
                            className="suggestion-item"
                            onClick={() => sendMessage(question)}
                        >
                            {question}
                        </li>
                    ))}
                </ul>
            </div>

            <div className="chatbox">
                {/* Thêm ref vào chat-body để theo dõi cuộn */}
                <div className="chat-body" ref={chatBodyRef}>
                    {/* Hiển thị trạng thái tải lịch sử ban đầu */}
                    {historyLoading && messages.length === 0 && (
                        <div className="system-msg">
                            <em>⏳ Đang tải lịch sử trò chuyện...</em>
                        </div>
                    )}

                    {/* Hiển thị trạng thái tải tin nhắn cũ hơn khi cuộn */}
                    {!historyLoading &&
                        isLoadingOldMessages &&
                        hasMoreHistory && (
                            <div className="system-msg">
                                <em>⏳ Đang tải tin nhắn cũ hơn...</em>
                            </div>
                        )}

                    {messages.map((msg, i) =>
                        msg.sender === "system" ? (
                            <div key={i} className="system-msg">
                                <em>{msg.text}</em>
                            </div>
                        ) : (
                            <div
                                key={i}
                                className={`chat-message-row ${
                                    msg.sender === "user" ? "left" : "right"
                                }`}
                            >
                                {msg.sender === "user" && (
                                    <span className="icon">👤</span>
                                )}
                                <div
                                    className={
                                        msg.sender === "user"
                                            ? "user-msg"
                                            : "bot-msg"
                                    }
                                    dangerouslySetInnerHTML={{
                                        __html: msg.text.replace(
                                            /(https?:\/\/[^\s]+)/g,
                                            (url) =>
                                                `<a href="${url}" target="_blank" rel="noopener noreferrer" style="color:blue">${url}</a>`
                                        ),
                                    }}
                                ></div>
                                {msg.sender === "bot" && (
                                    <span className="icon">🤖</span>
                                )}
                            </div>
                        )
                    )}

                    {botTyping && (
                        <div className="chat-message-row right">
                            <div className="bot-msg typing">
                                Đang trả lời...
                            </div>
                            <span className="icon">🤖</span>
                        </div>
                    )}

                    {audioLoading && (
                        <div className="system-msg">
                            <em>🎙️ Đang xử lý ghi âm...</em>
                        </div>
                    )}

                    <div ref={endRef}></div>
                </div>

                <div className="chat-input">
                    <input
                        value={input}
                        onChange={(e) => setInput(e.target.value)}
                        onKeyDown={(e) =>
                            e.key === "Enter" && sendMessage(input)
                        }
                        placeholder="Nhập câu hỏi..."
                    />
                    <button onClick={() => sendMessage(input)}>Gửi</button>
                    {/* <button onClick={() => setShowRecorder(true)}>🎤</button> */}
                </div>

                {/* {showRecorder && (
                    <AudioRecorderModal
                        onClose={() => setShowRecorder(false)}
                        onSend={handleSendAudio}
                    />
                )} */}
            </div>
        </div>
    );
}
