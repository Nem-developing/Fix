import React from "react";
import { NavLink } from "react-router-dom";
import { routeConfig } from "../routes/routeConfig";

const Sidebar = () => {
  return (
    <div className="Sidebar">
      <div>
        <h1>Nouveau ticketo</h1>
      </div>
      <div className="separation"/>
      <nav>
        {routeConfig.map(({ path, label }) => (
          <NavLink key={path} to={path}>
            <h1>{label}</h1>
          </NavLink>
        ))}
      </nav>
    </div>
  );
};

export default Sidebar;
