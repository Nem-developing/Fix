import React from "react";
import ReactDOM from "react-dom/client";
import App from "./App";
import "./styles/allcss";

const root = ReactDOM.createRoot(document.getElementById("root")!);
root.render(
  // ========================= Supprimer le "React.StrictMode" en production =========================
  <React.StrictMode>
    <App />
  </React.StrictMode>
);
