import React from "react";

type NavbarProps = {
  onBurgerClick: () => void;
  sidebarOpen: boolean;
};

const Navbar: React.FC<NavbarProps> = ({ onBurgerClick, sidebarOpen }) => {
  return (
    <header className="Navbar">
      <button
        className={`burger ${sidebarOpen ? "open" : ""}`}
        aria-label="Toggle sidebar menu"
        onClick={onBurgerClick}
      >
        <span></span>
        <span></span>
        <span></span>
      </button>
    </header>
  );
};

export default Navbar;
