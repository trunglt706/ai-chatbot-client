import React, { useRef, useEffect, useState, useCallback } from "react";
import * as faceapi from "face-api.js";
import { ToastContainer, toast } from "react-toastify"; // Import Toastr
import "react-toastify/dist/ReactToastify.css"; // Import CSS của Toastr
import "../../css/face/index.css"; // File CSS cơ bản

function App() {
    const imageRef = useRef();
    const canvasRef = useRef();
    const [selectedImage, setSelectedImage] = useState(null);
    const [detectedFaces, setDetectedFaces] = useState([]);
    const [loadingModels, setLoadingModels] = useState(true);
    const [errorMessage, setErrorMessage] = useState("");
    const [isProcessing, setIsProcessing] = useState(false); // Trạng thái đang xử lý

    // 1. Load các mô hình của Face-API.js khi component mount
    useEffect(() => {
        const loadModels = async () => {
            const MODEL_URL = "./models";
            try {
                await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
                await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
                await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
                await faceapi.nets.ageGenderNet.loadFromUri(MODEL_URL); // Thêm mô hình tuổi/giới tính
                await faceapi.nets.faceExpressionNet.loadFromUri(MODEL_URL); // Thêm mô hình biểu cảm
                setLoadingModels(false);
                console.log("Face-API models loaded successfully!");
            } catch (error) {
                console.error("Error loading Face-API models:", error);
                setErrorMessage(
                    "Không thể tải các mô hình nhận diện khuôn mặt. Vui lòng kiểm tra đường dẫn và kết nối mạng."
                );
                setLoadingModels(false);
            }
        };
        loadModels();
    }, []);

    // 2. Gửi khuôn mặt đã cắt lên Backend - Đã tách thành useCallback để sử dụng nội bộ
    const sendFacesToBackend = useCallback(async (facesToProcess) => {
        if (facesToProcess.length === 0) {
            toast.warn("Không có khuôn mặt nào được phát hiện để gửi.");
            return;
        }

        const image = imageRef.current;
        if (!image) return;

        setIsProcessing(true); // Bắt đầu xử lý
        let facesSentCount = 0;

        try {
            // Duyệt qua từng khuôn mặt đã phát hiện
            for (let i = 0; i < facesToProcess.length; i++) {
                const detection = facesToProcess[i];
                const { x, y, width, height } = detection.detection.box;

                // Tạo một canvas tạm thời để vẽ khuôn mặt đã cắt
                const faceCanvas = document.createElement("canvas");
                faceCanvas.width = width;
                faceCanvas.height = height;
                const faceCtx = faceCanvas.getContext("2d");

                // Vẽ phần khuôn mặt từ ảnh gốc lên canvas tạm thời
                faceCtx.drawImage(
                    image,
                    x,
                    y,
                    width,
                    height,
                    0,
                    0,
                    width,
                    height
                );

                // Chuyển canvas thành Blob (file ảnh)
                await new Promise((resolve) => {
                    faceCanvas.toBlob(
                        async (blob) => {
                            if (blob) {
                                const formData = new FormData();
                                formData.append(
                                    "face_image",
                                    blob,
                                    `face_${Date.now()}_${i}.jpg`
                                );

                                // Thêm thông tin client-side detection (tuổi, giới tính, biểu cảm) nếu muốn gửi
                                // Lưu ý: Server có thể tự tính toán lại nếu cần độ chính xác cao hơn
                                if (detection.age && detection.gender) {
                                    formData.append(
                                        "age",
                                        detection.age.toFixed(0)
                                    );
                                    formData.append("gender", detection.gender);
                                }
                                if (detection.expressions) {
                                    const dominantExpression = Object.keys(
                                        detection.expressions
                                    ).reduce((a, b) =>
                                        detection.expressions[a] >
                                        detection.expressions[b]
                                            ? a
                                            : b
                                    );
                                    formData.append(
                                        "expression",
                                        dominantExpression
                                    );
                                }

                                // Gửi dữ liệu lên Backend
                                try {
                                    // Thay đổi URL này thành API Endpoint của Backend Python của bạn
                                    const backendUrl = "face/check-in";
                                    const response = await fetch(backendUrl, {
                                        method: "POST",
                                        body: formData,
                                        // headers: {
                                        //   'Authorization': 'Bearer YOUR_AUTH_TOKEN' // Nếu bạn có xác thực API
                                        // }
                                    });

                                    if (response.ok) {
                                        const result = await response.json();
                                        console.log(
                                            "Backend response for face:",
                                            result
                                        );
                                        facesSentCount++;
                                        // Hiển thị toastr với thông tin từ server
                                        toast.success(
                                            <div className="toastr-content">
                                                <p>Khuôn mặt {i + 1}:</p>
                                                <p>
                                                    Tên:{" "}
                                                    <strong>
                                                        {result.name ||
                                                            "Không xác định"}
                                                    </strong>
                                                </p>
                                                <p>
                                                    Tuổi:{" "}
                                                    <strong>
                                                        {result.age || "N/A"}
                                                    </strong>
                                                </p>
                                                <p>
                                                    Độ nhận diện:{" "}
                                                    <strong>
                                                        {(
                                                            result.confidence *
                                                            100
                                                        ).toFixed(2)}
                                                        %
                                                    </strong>
                                                </p>
                                            </div>,
                                            { autoClose: 5000 }
                                        );
                                    } else {
                                        const errorData = await response.json();
                                        console.error(
                                            "Error sending face to backend:",
                                            response.status,
                                            errorData
                                        );
                                        toast.error(
                                            `Lỗi gửi khuôn mặt ${i + 1}: ${
                                                errorData.message ||
                                                response.statusText
                                            }`
                                        );
                                    }
                                } catch (fetchError) {
                                    console.error(
                                        "Network error sending face:",
                                        fetchError
                                    );
                                    toast.error(
                                        `Lỗi mạng khi gửi khuôn mặt ${i + 1}.`
                                    );
                                }
                            }
                            resolve(); // Quan trọng: Giải quyết Promise để vòng lặp có thể tiếp tục
                        },
                        "image/jpeg",
                        0.9
                    ); // Định dạng JPEG với chất lượng 90%
                });
            }
            if (facesSentCount > 0) {
                toast.info(
                    `Đã gửi thành công ${facesSentCount} khuôn mặt đến Backend.`
                );
            }
        } catch (error) {
            console.error("Error processing faces for sending:", error);
            toast.error("Lỗi khi chuẩn bị gửi khuôn mặt. Vui lòng thử lại.");
        } finally {
            setIsProcessing(false); // Kết thúc xử lý
        }
    }, []); // Không có dependencies để hàm không bị tạo lại không cần thiết

    // 3. Xử lý khi người dùng chọn ảnh
    const handleImageUpload = (event) => {
        const file = event.target.files[0];
        if (file) {
            setSelectedImage(URL.createObjectURL(file));
            setDetectedFaces([]); // Reset detected faces
            setErrorMessage(""); // Reset error message
            setIsProcessing(false); // Reset trạng thái xử lý
        }
    };

    // 4. Phát hiện khuôn mặt và tự động gọi gửi lên Backend
    const handleImageLoad = async () => {
        if (loadingModels || !selectedImage || isProcessing) {
            return;
        }

        const image = imageRef.current;
        const canvas = canvasRef.current;

        if (!image || !canvas) {
            return;
        }

        setIsProcessing(true); // Đặt trạng thái đang xử lý
        toast.info("Đang phát hiện khuôn mặt và gửi lên server...", {
            autoClose: 3000,
        });

        // Set canvas dimensions to match the image
        canvas.width = image.width;
        canvas.height = image.height;

        try {
            const detections = await faceapi
                .detectAllFaces(image, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceDescriptors()
                .withAgeAndGender() // Lấy tuổi và giới tính
                .withFaceExpressions(); // Lấy biểu cảm

            // Hiển thị bounding boxes lên canvas
            const displaySize = { width: image.width, height: image.height };
            faceapi.matchDimensions(canvas, displaySize);

            const resizedDetections = faceapi.resizeResults(
                detections,
                displaySize
            );
            setDetectedFaces(resizedDetections);

            const ctx = canvas.getContext("2d");
            ctx.clearRect(0, 0, canvas.width, canvas.height); // Xóa canvas cũ
            faceapi.draw.drawDetections(canvas, resizedDetections);
            faceapi.draw.drawFaceLandmarks(canvas, resizedDetections);
            // Bạn có thể vẽ thêm thông tin tuổi/giới tính/biểu cảm lên canvas nếu muốn
            resizedDetections.forEach((detection) => {
                const box = detection.detection.box;
                const text = `${detection.gender || ""} ${
                    detection.age ? detection.age.toFixed(0) : ""
                }`;
                new faceapi.draw.DrawTextField([text], box.bottomLeft).draw(
                    canvas
                );
            });

            if (resizedDetections.length > 0) {
                // Tự động gọi hàm gửi lên Backend
                await sendFacesToBackend(resizedDetections);
            } else {
                toast.warn("Không tìm thấy khuôn mặt nào trong ảnh.", {
                    autoClose: 3000,
                });
            }
        } catch (error) {
            console.error("Error detecting faces:", error);
            setErrorMessage("Lỗi khi phát hiện khuôn mặt. Vui lòng thử lại.");
            toast.error("Lỗi khi phát hiện khuôn mặt.");
        } finally {
            setIsProcessing(false); // Kết thúc xử lý
        }
    };

    return (
        <div className="App">
            <h1>Phát hiện & Nhận diện Khuôn mặt</h1>

            {loadingModels ? (
                <p>Đang tải các mô hình nhận diện khuôn mặt... Vui lòng chờ.</p>
            ) : errorMessage ? (
                <p className="error-message">{errorMessage}</p>
            ) : (
                <>
                    <input
                        type="file"
                        accept="image/*"
                        onChange={handleImageUpload}
                        disabled={isProcessing}
                    />

                    {selectedImage && (
                        <div className="image-container">
                            <img
                                ref={imageRef}
                                src={selectedImage}
                                alt="Selected"
                                onLoad={handleImageLoad}
                                style={{ maxWidth: "100%", height: "auto" }}
                            />
                            <canvas ref={canvasRef} className="face-canvas" />
                        </div>
                    )}

                    {selectedImage &&
                        detectedFaces.length === 0 &&
                        !loadingModels &&
                        !errorMessage &&
                        !isProcessing && (
                            <p>Chọn ảnh để bắt đầu phát hiện khuôn mặt.</p>
                        )}

                    {isProcessing && <p>Đang xử lý...</p>}
                </>
            )}

            {/* Container cho Toastr */}
            <ToastContainer position="bottom-right" />
        </div>
    );
}

export default App;
