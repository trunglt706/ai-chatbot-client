import React from "react";
import ReactDOM from "react-dom/client";
import ChatBotStream from "./chatbot/ChatBotStream";

const container = document.getElementById("chat-support");
if (container) {
    const root = ReactDOM.createRoot(container);
    root.render(<ChatBotStream />);
}
