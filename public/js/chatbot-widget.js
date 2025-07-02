(function () {
    // === Cấu hình (Người dùng có thể thay đổi) ===
    const CHATBOT_CONFIG = {
        apiUrl: "/chatbot-auto", // THAY THẾ BẰU ĐỊA CHỈ API CỦA BẠN
        title: "Trò chuyện với tôi!",
        subtitle: "Tôi mà Mona Bot.",
        welcomeMessage: "Chào mừng bạn! Tôi có thể giúp gì cho bạn hôm nay?",
        initialMessagePlaceholder: "Xin chào!",
        sendButtonText: "Gửi",
        toggleButtonText: "Chat", // Văn bản hiển thị trên nút toggle
        themeColor: "#4F46E5", // Màu chủ đạo (ví dụ: indigo-600)
        textColor: "#FFFFFF", // Màu chữ trên themeColor
        messageBubbleColor: "#6366F1", // Màu tin nhắn của bot (ví dụ: indigo-500)
        userBubbleColor: "#E0E7FF", // Màu tin nhắn của người dùng (ví dụ: indigo-100)
        userBubbleTextColor: "#1F2937", // Màu chữ tin nhắn của người dùng
    };

    // Khóa localStorage để lưu vị trí
    const STORAGE_KEY = "miniChatbotPosition";
    // ===========================================

    let chatboxContainer = null; // Đây là #mini-chatbot
    let chatboxHeader = null;
    let chatboxBody = null;
    let chatboxInput = null;
    let chatboxToggleButton = null;
    let chatboxWrapper = null; // Đây là khung chat chính

    // Biến cho chức năng kéo thả
    let isDragging = false;
    let offsetX, offsetY;

    // === Hàm khởi tạo Chatbot ===
    function initializeChatbot() {
        // 1. Tạo HTML cho Chatbot
        createChatbotHTML();

        // 2. Tải vị trí đã lưu hoặc đặt mặc định
        loadAndSetPosition();

        // 3. Thêm CSS cơ bản
        addChatbotCSS();

        // 4. Gán sự kiện
        addEventListeners();
    }

    function createChatbotHTML() {
        // Container chính bọc tất cả để tránh xung đột CSS
        chatboxContainer = document.createElement("div");
        chatboxContainer.id = "mini-chatbot"; // ID bao bọc chính

        // Nút để mở/đóng chatbot (icon chat nhỏ ban đầu)
        chatboxToggleButton = document.createElement("button");
        chatboxToggleButton.id = "mini-chatbot-toggle-button";
        chatboxToggleButton.textContent = CHATBOT_CONFIG.toggleButtonText;
        chatboxToggleButton.style.backgroundColor = CHATBOT_CONFIG.themeColor;
        chatboxToggleButton.style.color = CHATBOT_CONFIG.textColor;
        // Bắt đầu hiển thị nút toggle, ẩn khung chat
        chatboxToggleButton.classList.add("visible");
        chatboxToggleButton.classList.remove("hidden");

        // Wrapper chứa khung chatbot chính
        chatboxWrapper = document.createElement("div");
        chatboxWrapper.id = "mini-chatbot-wrapper";
        chatboxWrapper.classList.add("hidden"); // Mặc định ẩn khung chat

        // Header của khung chat
        chatboxHeader = document.createElement("div");
        chatboxHeader.id = "mini-chatbot-header";
        chatboxHeader.style.backgroundColor = CHATBOT_CONFIG.themeColor;
        chatboxHeader.style.color = CHATBOT_CONFIG.textColor;
        chatboxHeader.innerHTML = `
            <h3>${CHATBOT_CONFIG.title}</h3>
            <p>${CHATBOT_CONFIG.subtitle}</p>
            <button id="mini-chatbot-close-button">&times;</button>
        `;

        // Body của khung chat (nơi hiển thị tin nhắn)
        chatboxBody = document.createElement("div");
        chatboxBody.id = "mini-chatbot-body";
        chatboxBody.innerHTML = `<div class="mini-chatbot-message mini-chatbot-bot-message" style="background-color: ${CHATBOT_CONFIG.messageBubbleColor}; color: ${CHATBOT_CONFIG.textColor};">${CHATBOT_CONFIG.welcomeMessage}</div>`;

        // Footer của khung chat (input và nút gửi)
        const chatboxFooter = document.createElement("div");
        chatboxFooter.id = "mini-chatbot-footer";
        chatboxFooter.innerHTML = `
            <input type="text" id="mini-chatbot-input" placeholder="${CHATBOT_CONFIG.initialMessagePlaceholder}">
            <button id="mini-chatbot-send-button" style="background-color: ${CHATBOT_CONFIG.themeColor}; color: ${CHATBOT_CONFIG.textColor};">${CHATBOT_CONFIG.sendButtonText}</button>
        `;
        chatboxInput = chatboxFooter.querySelector("#mini-chatbot-input");

        // Gắn các phần tử vào cây DOM
        chatboxWrapper.appendChild(chatboxHeader);
        chatboxWrapper.appendChild(chatboxBody);
        chatboxWrapper.appendChild(chatboxFooter);

        chatboxContainer.appendChild(chatboxToggleButton);
        chatboxContainer.appendChild(chatboxWrapper);

        document.body.appendChild(chatboxContainer);
    }

    function addChatbotCSS() {
        const style = document.createElement("style");
        style.textContent = `
            /* Wrapper chính cho toàn bộ Chatbot */
            #mini-chatbot {
                position: fixed;
                font-family: Arial, sans-serif;
                z-index: 10000; /* Đảm bảo nổi trên các phần tử khác */
                display: flex;
                flex-direction: column;
                align-items: flex-end; /* Căn nút toggle và khung chat về phía bên phải */
                cursor: grab; /* Con trỏ kéo thả */
            }

            /* Nút Bật/Tắt Chatbot */
            #mini-chatbot-toggle-button {
                width: 60px;
                height: 60px;
                border-radius: 50%;
                border: none;
                font-size: 16px;
                cursor: pointer;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                transition: all 0.3s ease;
                animation: mini-chatbot-pulse 2s infinite; /* Hiệu ứng nhấp nháy */
                display: flex; /* Dùng flex để căn giữa chữ */
                justify-content: center;
                align-items: center;
            }

            #mini-chatbot-toggle-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3);
            }

            /* Khung Chatbot chính */
            #mini-chatbot-wrapper {
                width: 320px;
                height: 400px;
                background-color: #f8f9fa; /* Light gray background */
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
                display: flex;
                flex-direction: column;
                overflow: hidden;
                margin-top: 10px; /* Khoảng cách giữa nút toggle và khung chat nếu cần */
                transition: all 0.3s ease;
            }

            /* Lớp ẩn/hiện */
            #mini-chatbot-wrapper.hidden,
            #mini-chatbot-toggle-button.hidden {
                display: none !important;
            }

            #mini-chatbot-wrapper.visible,
            #mini-chatbot-toggle-button.visible {
                display: flex !important; /* Dùng flex để maintain layout */
            }


            /* Header của Chatbot */
            #mini-chatbot-header {
                padding: 15px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.2);
                position: relative;
            }

            #mini-chatbot-header h3 {
                margin: 0;
                font-size: 18px;
                font-weight: bold;
            }

            #mini-chatbot-header p {
                margin: 0;
                font-size: 13px;
                opacity: 0.8;
            }

            #mini-chatbot-close-button {
                position: absolute;
                top: 10px;
                right: 10px;
                background: none;
                border: none;
                font-size: 24px;
                color: ${CHATBOT_CONFIG.textColor};
                cursor: pointer;
                line-height: 1; /* Căn giữa ký tự X */
            }

            /* Body của Chatbot (Tin nhắn) */
            #mini-chatbot-body {
                flex-grow: 1;
                padding: 15px;
                overflow-y: auto;
                background-color: #FFFFFF; /* White background for messages */
                display: flex; /* Để căn tin nhắn */
                flex-direction: column; /* Tin nhắn xếp chồng lên nhau */
            }

            /* Kiểu dáng tin nhắn chung */
            .mini-chatbot-message {
                padding: 10px 15px;
                border-radius: 18px;
                margin-bottom: 10px;
                max-width: 80%;
                word-wrap: break-word;
            }

            /* Tin nhắn của Bot */
            .mini-chatbot-bot-message {
                align-self: flex-start; /* Tin nhắn bot căn trái */
                margin-right: auto;
                background-color: #fff1e0;
            }

            /* Tin nhắn của Người dùng */
            .mini-chatbot-user-message {
                align-self: flex-end; /* Tin nhắn user căn phải */
                margin-left: auto;
                background-color: ${CHATBOT_CONFIG.userBubbleColor};
                color: ${CHATBOT_CONFIG.userBubbleTextColor};
            }

            /* Footer của Chatbot (Input và nút gửi) */
            #mini-chatbot-footer {
                display: flex;
                border-top: 1px solid #e0e0e0;
                padding: 10px;
                background-color: #FFFFFF;
            }

            #mini-chatbot-input {
                flex-grow: 1;
                border: 1px solid #cccccc;
                border-radius: 20px;
                padding: 8px 15px;
                font-size: 14px;
                outline: none;
                margin-right: 8px;
            }

            #mini-chatbot-send-button {
                padding: 8px 15px;
                border-radius: 20px;
                border: none;
                cursor: pointer;
                font-size: 14px;
                font-weight: bold;
                transition: background-color 0.2s ease;
            }

            #mini-chatbot-send-button:hover {
                filter: brightness(1.1);
            }

            /* Keyframes cho hiệu ứng nhấp nháy */
            @keyframes mini-chatbot-pulse {
                0% {
                    box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.7);
                }
                70% {
                    box-shadow: 0 0 0 10px rgba(79, 70, 229, 0);
                }
                100% {
                    box-shadow: 0 0 0 0 rgba(79, 70, 229, 0);
                }
            }
        `;
        document.head.appendChild(style);
    }

    // === Xử lý Kéo thả ===
    function startDragging(e) {
        // Chỉ cho phép kéo khi bắt đầu từ chatboxToggleButton hoặc chatboxWrapper
        if (
            e.target.closest("#mini-chatbot-toggle-button") ||
            e.target.closest("#mini-chatbot-wrapper")
        ) {
            isDragging = true;
            chatboxContainer.style.cursor = "grabbing";

            const rect = chatboxContainer.getBoundingClientRect();
            offsetX = e.clientX - rect.left;
            offsetY = e.clientY - rect.top;

            // Ngăn việc chọn văn bản khi kéo
            e.preventDefault();
        }
    }

    function doDragging(e) {
        if (!isDragging) return;

        let newLeft = e.clientX - offsetX;
        let newTop = e.clientY - offsetY;

        // Giới hạn trong viewport
        newLeft = Math.max(
            0,
            Math.min(newLeft, window.innerWidth - chatboxContainer.offsetWidth)
        );
        newTop = Math.max(
            0,
            Math.min(newTop, window.innerHeight - chatboxContainer.offsetHeight)
        );

        chatboxContainer.style.left = `${newLeft}px`;
        chatboxContainer.style.top = `${newTop}px`;
        chatboxContainer.style.right = "auto"; // Vô hiệu hóa right/bottom khi kéo
        chatboxContainer.style.bottom = "auto";
    }

    function stopDragging() {
        if (!isDragging) return;
        isDragging = false;
        chatboxContainer.style.cursor = "grab";

        // Tính toán vị trí tương đối từ cạnh phải và dưới để lưu
        const rect = chatboxContainer.getBoundingClientRect();
        const right = window.innerWidth - (rect.left + rect.width);
        const bottom = window.innerHeight - (rect.top + rect.height);

        savePosition(right, bottom);
    }

    function savePosition(right, bottom) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify({ right, bottom }));
    }

    function loadAndSetPosition() {
        const savedPosition = localStorage.getItem(STORAGE_KEY);
        if (savedPosition) {
            const { right, bottom } = JSON.parse(savedPosition);
            chatboxContainer.style.right = `${right}px`;
            chatboxContainer.style.bottom = `${bottom}px`;
            // Vô hiệu hóa top/left nếu đang dùng right/bottom
            chatboxContainer.style.left = "auto";
            chatboxContainer.style.top = "auto";
        } else {
            // Vị trí mặc định: góc dưới bên phải
            chatboxContainer.style.right = "20px";
            chatboxContainer.style.bottom = "20px";
            chatboxContainer.style.left = "auto";
            chatboxContainer.style.top = "auto";
        }
    }
    // === Hết xử lý Kéo thả ===

    function addEventListeners() {
        chatboxToggleButton.addEventListener("click", toggleChatbox);
        chatboxHeader
            .querySelector("#mini-chatbot-close-button")
            .addEventListener("click", toggleChatbox);
        chatboxInput.addEventListener("keypress", function (e) {
            if (e.key === "Enter") {
                sendMessage();
            }
        });
        document
            .getElementById("mini-chatbot-send-button")
            .addEventListener("click", sendMessage);

        // // Sự kiện kéo thả cho #mini-chatbot-toggle-button
        // chatboxToggleButton.addEventListener("mousedown", startDragging);
        // // 👉 Thêm sự kiện kéo cho khung chat (wrapper)
        // chatboxWrapper.addEventListener("mousedown", startDragging);

        // document.addEventListener("mousemove", doDragging);
        // document.addEventListener("mouseup", stopDragging);
    }

    function toggleChatbox() {
        // Nếu khung chat đang ẩn, hiển thị nó và ẩn nút toggle
        if (chatboxWrapper.classList.contains("hidden")) {
            chatboxWrapper.classList.remove("hidden");
            chatboxWrapper.classList.add("visible");
            chatboxToggleButton.classList.remove("visible");
            chatboxToggleButton.classList.add("hidden");
            chatboxInput.focus();
            chatboxBody.scrollTop = chatboxBody.scrollHeight; // Cuộn xuống cuối khi mở
        } else {
            // Nếu khung chat đang hiện, ẩn nó và hiển thị nút toggle
            chatboxWrapper.classList.remove("visible");
            chatboxWrapper.classList.add("hidden");
            chatboxToggleButton.classList.remove("hidden");
            chatboxToggleButton.classList.add("visible");
            // Khi đóng chatbox, container sẽ quay về vị trí của nút toggle
            // Vị trí đã lưu chỉ áp dụng cho nút toggle
            loadAndSetPosition();
        }
    }

    function addMessage(text, sender) {
        const messageDiv = document.createElement("div");
        messageDiv.classList.add(
            "mini-chatbot-message",
            "mini-chatbot-" + sender + "-message"
        );
        messageDiv.textContent = text;
        chatboxBody.appendChild(messageDiv);
        chatboxBody.scrollTop = chatboxBody.scrollHeight; // Cuộn xuống cuối
    }

    async function sendMessage() {
        const userMessage = chatboxInput.value.trim();
        if (!userMessage) return;

        addMessage(userMessage, "user");
        chatboxInput.value = "";
        chatboxInput.disabled = true; // Vô hiệu hóa input khi đang gửi
        document.getElementById("mini-chatbot-send-button").disabled = true;

        try {
            // Hiển thị trạng thái đang gõ
            const typingIndicator = document.createElement("div");
            typingIndicator.classList.add(
                "mini-chatbot-message",
                "mini-chatbot-bot-message"
            );
            typingIndicator.style.backgroundColor =
                CHATBOT_CONFIG.messageBubbleColor;
            typingIndicator.style.color = CHATBOT_CONFIG.textColor;
            typingIndicator.innerHTML = "Đang gõ..."; // Hoặc dùng animation ba chấm
            chatboxBody.appendChild(typingIndicator);
            chatboxBody.scrollTop = chatboxBody.scrollHeight;

            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content");
            const response = await fetch(CHATBOT_CONFIG.apiUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({
                    question: userMessage,
                    type: "not_train",
                }),
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const responseData = await response.json();
            console.log("responseData", responseData);

            // Xóa trạng thái đang gõ
            chatboxBody.removeChild(typingIndicator);

            addMessage(
                responseData?.message || "Xin lỗi, tôi không hiểu.",
                "bot"
            ); // `data.reply` là trường chứa câu trả lời từ API của bạn
        } catch (error) {
            console.error("Lỗi khi gửi tin nhắn:", error);
            // Xóa trạng thái đang gõ nếu có lỗi
            const typingIndicator = chatboxBody.querySelector(
                ".mini-chatbot-bot-message:last-child"
            );
            if (typingIndicator && typingIndicator.innerHTML === "Đang gõ...") {
                chatboxBody.removeChild(typingIndicator);
            }
            addMessage("Rất tiếc, có lỗi xảy ra. Vui lòng thử lại sau.", "bot");
        } finally {
            chatboxInput.disabled = false; // Kích hoạt lại input
            document.getElementById(
                "mini-chatbot-send-button"
            ).disabled = false;
            chatboxInput.focus();
        }
    }

    // Chạy hàm khởi tạo khi DOM đã sẵn sàng
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initializeChatbot);
    } else {
        initializeChatbot();
    }
})();
