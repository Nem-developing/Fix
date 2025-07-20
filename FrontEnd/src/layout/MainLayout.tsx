import React, { useState } from "react";
import Sidebar from "../components/navigation/Sidebar";
import Navbar from "../components/navigation/Navbar";

const MainLayout = ({ children }: { children: React.ReactNode }) => {
  const [sidebarOpen, setSidebarOpen] = useState(false);

  const toggleSidebar = () => setSidebarOpen((open) => !open);
  const closeSidebar = () => sidebarOpen && setSidebarOpen(false);

  return (
    <div className="MainLayout">
      <Navbar onBurgerClick={toggleSidebar} sidebarOpen={sidebarOpen} />
      <Sidebar isOpen={sidebarOpen} onClose={closeSidebar} />
      <div className="Pages" onClick={closeSidebar}>
        <main>{children}</main>
      </div>
    </div>
  );
};

export default MainLayout;
