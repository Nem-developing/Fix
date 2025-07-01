import React from "react";
import { NavLink } from "react-router-dom";
import { routeConfig } from "../routes/routeConfig";

const Sidebar = () => {
  return (
    <div className="Sidebar">
      {/* <div>
        <h1>Nouveau ticketo</h1>
      </div>
      <div className="separation" /> */}
      <nav>
        {routeConfig.map(({ path, label, icon }, index) => (
          <NavLink key={path} to={path}>
            <div className={`imagelien ${index === 0 ? "with-separator" : ""}`}>
              <img src={icon} alt={label} />
              <h1>{label}</h1>
            </div>
          </NavLink>
        ))}
      </nav>
    </div>
  );
};

export default Sidebar;
