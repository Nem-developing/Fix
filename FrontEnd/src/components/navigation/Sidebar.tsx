import React from "react";
import { NavLink } from "react-router-dom";
import { routeConfig } from "../../routes/routeConfig";

type SidebarProps = {
  isOpen: boolean;
  onClose?: () => void;
};

const Sidebar: React.FC<SidebarProps> = ({ isOpen, onClose }) => {
  return (
    <div className={`Sidebar ${isOpen ? "open" : ""}`}>
      <div className="SidebarContent">
        <nav>
          {routeConfig.map(({ path, label, icon }, index) => (
            <NavLink key={path} to={path} onClick={onClose}>
              <div
                className={`imagelien ${index === 0 ? "with-separator" : ""}`}
              >
                <img src={icon} alt={label} />
                <h1>{label}</h1>
              </div>
            </NavLink>
          ))}
        </nav>
      </div>
    </div>
  );
};

export default Sidebar;
