import React, { useState, useRef, useEffect } from "react";
import "../../css/chatbot/AudioRecorderModal.css";

const AudioRecorderModal = ({ onClose, onSend }) => {
    const [mediaRecorder, setMediaRecorder] = useState(null);
    const [isSending, setIsSending] = useState(false);
    const [isRecording, setIsRecording] = useState(false);
    const [audioBlob, setAudioBlob] = useState(null);
    const [audioURL, setAudioURL] = useState(null);
    // Đã loại bỏ state transcribedText

    // Refs để lưu trữ các đối tượng và ID animation
    const streamRef = useRef(null);
    const audioRef = useRef(null);
    const recordedChunksRef = useRef([]);
    const canvasRef = useRef(null);
    const animationIdRef = useRef(null); // Lưu trữ ID của requestAnimationFrame
    const audioContextRef = useRef(null);
    const analyserRef = useRef(null);
    const dataArrayRef = useRef(null);
    const sourceNodeRef = useRef(null); // Đổi tên để tránh nhầm lẫn với biến `source` cục bộ

    useEffect(() => {
        // Yêu cầu quyền truy cập microphone của người dùng
        navigator.mediaDevices
            .getUserMedia({ audio: true })
            .then((stream) => {
                streamRef.current = stream; // Lưu trữ media stream
                const recorder = new MediaRecorder(stream); // Tạo một MediaRecorder mới
                setMediaRecorder(recorder); // Cập nhật state của media recorder

                // Listener cho sự kiện khi có dữ liệu âm thanh
                recorder.ondataavailable = (e) => {
                    if (e.data.size > 0) {
                        recordedChunksRef.current.push(e.data); // Thêm các chunk dữ liệu âm thanh
                    }
                };

                // Listener cho sự kiện khi ghi âm dừng
                recorder.onstop = () => {
                    // Tạo Blob từ các chunk âm thanh đã ghi
                    const blob = new Blob(recordedChunksRef.current, {
                        type: "audio/webm",
                    });
                    const url = URL.createObjectURL(blob); // Tạo URL cho blob âm thanh
                    setAudioBlob(blob); // Cập nhật state blob âm thanh
                    setAudioURL(url); // Cập nhật state URL âm thanh
                    recordedChunksRef.current = []; // Xóa các chunk đã ghi cho lần ghi tiếp theo

                    // Dừng animation sóng âm và đóng AudioContext nếu chúng đang hoạt động
                    if (animationIdRef.current) {
                        cancelAnimationFrame(animationIdRef.current);
                        animationIdRef.current = null;
                    }
                    if (sourceNodeRef.current) {
                        sourceNodeRef.current.disconnect(); // Ngắt kết nối source node
                        sourceNodeRef.current = null;
                    }
                    if (analyserRef.current) {
                        analyserRef.current.disconnect(); // Ngắt kết nối analyser node
                        analyserRef.current = null;
                    }
                    // CHỈ đóng AudioContext nếu nó tồn tại và chưa bị đóng
                    if (
                        audioContextRef.current &&
                        audioContextRef.current.state !== "closed"
                    ) {
                        audioContextRef.current.close(); // Đóng audio context
                        audioContextRef.current = null;
                    }
                };
            })
            .catch((err) => {
                console.error("Lỗi khi truy cập micro:", err);
                // Có thể hiển thị thông báo lỗi thân thiện hơn cho người dùng ở đây
                // Ví dụ: alert("Không thể truy cập microphone. Vui lòng kiểm tra quyền truy cập.");
            });

        // Hàm dọn dẹp khi component bị unmount
        return () => {
            if (animationIdRef.current) {
                cancelAnimationFrame(animationIdRef.current);
            }
            if (sourceNodeRef.current) {
                sourceNodeRef.current.disconnect();
            }
            if (analyserRef.current) {
                analyserRef.current.disconnect();
            }
            // CHỈ đóng AudioContext nếu nó tồn tại và chưa bị đóng
            if (
                audioContextRef.current &&
                audioContextRef.current.state !== "closed"
            ) {
                audioContextRef.current.close();
            }
            if (streamRef.current) {
                streamRef.current.getTracks().forEach((track) => track.stop()); // Dừng tất cả các track media
            }
        };
    }, []); // Mảng dependency rỗng nghĩa là effect này chạy một lần khi mount và dọn dẹp khi unmount

    /**
     * Hàm vẽ sóng âm trên canvas.
     * Hàm này được gọi liên tục thông qua requestAnimationFrame.
     */
    const drawWaveform = () => {
        const canvas = canvasRef.current;
        const canvasCtx = canvas?.getContext("2d");
        const analyser = analyserRef.current;
        const dataArray = dataArrayRef.current;

        // Đảm bảo tất cả các phần tử cần thiết có sẵn trước khi vẽ
        if (!canvasCtx || !analyser || !dataArray) {
            return; // Không vẽ nếu thiếu bất kỳ ref nào
        }

        const WIDTH = canvas.width;
        const HEIGHT = canvas.height;

        // Lấy dữ liệu miền thời gian từ analyser
        analyser.getByteTimeDomainData(dataArray);

        // Xóa canvas
        canvasCtx.fillStyle = "rgb(255, 255, 255)"; // Nền trắng cho sóng âm
        canvasCtx.fillRect(0, 0, WIDTH, HEIGHT);

        // Thiết lập kiểu vẽ cho sóng âm
        canvasCtx.lineWidth = 2;
        canvasCtx.strokeStyle = "rgb(66, 135, 245)"; // Màu xanh cho sóng âm
        canvasCtx.beginPath(); // Bắt đầu một đường dẫn mới

        // Tính toán chiều rộng lát cắt cho mỗi điểm dữ liệu
        let sliceWidth = (WIDTH * 1.0) / dataArray.length;
        let x = 0;

        // Lặp qua mảng dữ liệu và vẽ sóng âm
        for (let i = 0; i < dataArray.length; i++) {
            // Chuẩn hóa giá trị dữ liệu (0-255) thành chiều cao canvas
            let v = dataArray[i] / 128.0; // Chuẩn hóa thành 0-2
            let y = (v * HEIGHT) / 2; // Ánh xạ tới chiều cao canvas

            if (i === 0) {
                canvasCtx.moveTo(x, y); // Di chuyển đến điểm đầu tiên
            } else {
                canvasCtx.lineTo(x, y); // Vẽ đường thẳng đến điểm hiện tại
            }

            x += sliceWidth; // Tăng x cho điểm tiếp theo
        }

        // Vẽ một đường thẳng đến cuối canvas ở giữa chiều cao (để hoàn thiện thẩm mỹ)
        canvasCtx.lineTo(canvas.width, canvas.height / 2);
        canvasCtx.stroke(); // Hiển thị đường dẫn sóng âm

        // Yêu cầu frame animation tiếp theo nếu vẫn đang ghi âm
        if (isRecording) {
            animationIdRef.current = requestAnimationFrame(drawWaveform);
        }
    };

    // NEW useEffect để quản lý vòng lặp vẽ sóng âm
    useEffect(() => {
        // Nếu đang ghi âm và tất cả các refs cần thiết đã sẵn sàng, bắt đầu vẽ sóng âm
        if (
            isRecording &&
            canvasRef.current &&
            analyserRef.current &&
            dataArrayRef.current
        ) {
            // Bắt đầu vòng lặp requestAnimationFrame
            animationIdRef.current = requestAnimationFrame(drawWaveform);
        } else {
            // Nếu không ghi âm hoặc các refs chưa sẵn sàng, hủy animation hiện có
            if (animationIdRef.current) {
                cancelAnimationFrame(animationIdRef.current);
                animationIdRef.current = null;
            }
        }

        // Hàm dọn dẹp cho useEffect này, đảm bảo animation dừng khi dependency thay đổi
        return () => {
            if (animationIdRef.current) {
                cancelAnimationFrame(animationIdRef.current);
                animationIdRef.current = null;
            }
        };
    }, [
        isRecording,
        canvasRef.current,
        analyserRef.current,
        dataArrayRef.current,
    ]); // Dependencies để re-run effect

    /**
     * Bắt đầu quá trình ghi âm và khởi tạo các đối tượng AudioContext.
     */
    const startRecording = () => {
        if (mediaRecorder) {
            // Xóa dữ liệu âm thanh trước đó
            setAudioBlob(null);
            setAudioURL(null);
            // Đã loại bỏ setTranscribedText("")
            recordedChunksRef.current = [];

            // Bắt đầu MediaRecorder với timeslice 1000ms (1 giây)
            mediaRecorder.start(1000);
            setIsRecording(true); // Cập nhật state ghi âm thành true

            // Khởi tạo AudioContext và AnalyserNode để hiển thị sóng âm
            if (
                audioContextRef.current &&
                audioContextRef.current.state !== "closed"
            ) {
                audioContextRef.current.close();
            }
            const audioContext = new (window.AudioContext ||
                window.webkitAudioContext)();
            audioContextRef.current = audioContext;

            const analyser = audioContext.createAnalyser();
            analyser.fftSize = 2048;
            analyserRef.current = analyser;

            const bufferLength = analyser.frequencyBinCount;
            const dataArray = new Uint8Array(bufferLength);
            dataArrayRef.current = dataArray;

            // Tạo nguồn stream từ microphone
            const source = audioContext.createMediaStreamSource(
                streamRef.current
            );
            sourceNodeRef.current = source;

            // Kết nối source tới analyser, và analyser tới destination của audio context
            source.connect(analyser);
            analyser.connect(audioContext.destination);
        }
    };

    /**
     * Dừng quá trình ghi âm.
     */
    const stopRecording = () => {
        if (mediaRecorder && isRecording) {
            mediaRecorder.stop(); // Dừng MediaRecorder
            setIsRecording(false); // Cập nhật state ghi âm thành false
        }
    };

    /**
     * Xử lý việc gửi âm thanh đã ghi.
     */
    const handleSend = async () => {
        if (audioBlob && !isSending) {
            setIsSending(true); // Cập nhật state gửi thành true
            // Đã loại bỏ setTranscribedText("Đang phiên âm bằng ChatGPT...") và các bước xử lý API phiên dịch

            try {
                // Gọi prop onSend gốc với audioBlob để xử lý ở backend
                await onSend(audioBlob);
                onClose(); // Đóng modal sau khi gửi thành công
            } catch (error) {
                console.error("Lỗi khi gửi ghi âm:", error);
                alert("Gửi thất bại. Vui lòng thử lại.");
            } finally {
                setIsSending(false); // Đặt lại state gửi
            }
        }
    };

    return (
        <div className="modal-overlay">
            <div className="modal-content">
                <h3>🎙 Ghi âm tin nhắn</h3>

                {/* Hiển thị canvas để trực quan hóa sóng âm chỉ khi đang ghi âm */}
                {isRecording && (
                    <canvas
                        ref={canvasRef}
                        width={300} // Chiều rộng cố định
                        height={80} // Chiều cao cố định
                        style={{
                            borderRadius: "8px",
                            boxShadow: "0 2px 4px rgba(0,0,0,0.1)",
                            marginBottom: "15px",
                            border: "1px solid #ddd",
                        }}
                    />
                )}

                {/* Hiển thị trình phát âm thanh nếu đã ghi âm */}
                {audioURL && (
                    <audio
                        ref={audioRef}
                        src={audioURL}
                        controls // Hiển thị các điều khiển âm thanh mặc định
                        style={{
                            width: "100%",
                            marginBottom: 15,
                            borderRadius: "8px",
                        }}
                    />
                )}

                {/* Đã loại bỏ phần hiển thị văn bản phiên âm */}

                <div className="buttons">
                    {/* Nút Bắt đầu */}
                    {!isRecording && !isSending && (
                        <button onClick={startRecording} className="btn blue">
                            ▶️ Bắt đầu
                        </button>
                    )}
                    {/* Nút Kết thúc */}
                    {isRecording && !isSending && (
                        <button
                            onClick={stopRecording}
                            className="btn secondary"
                        >
                            ⏹ Kết thúc
                        </button>
                    )}

                    {/* Nút Gửi */}
                    {audioBlob && (
                        <button
                            onClick={handleSend}
                            className="btn green"
                            disabled={isSending} // Vô hiệu hóa trong khi đang gửi
                        >
                            {isSending ? "⏳ Đang gửi..." : "✅ Gửi"}
                        </button>
                    )}

                    {/* Nút Đóng */}
                    {!isSending && (
                        <button onClick={onClose} className="btn red">
                            ❌ Đóng
                        </button>
                    )}
                </div>
            </div>
        </div>
    );
};

export default AudioRecorderModal;
