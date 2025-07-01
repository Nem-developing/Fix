import React from "react";
import Sidebar from "../components/Sidebar";
import Navbar from "../components/Navbar";

const MainLayout = ({ children }: { children: React.ReactNode }) => {
  return (
    <div className="MainLayout">
      <Sidebar />
      <div className="Pages">
        <Navbar />
        <main>{children}</main>
      </div>
    </div>
  );
};

export default MainLayout;
