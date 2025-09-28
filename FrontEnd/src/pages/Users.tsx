import React from "react";
import Information_dev from "../components/autres/informationdev";
import ThemeSelector from "../components/utility/ThemeSelector";

const Users = () => {
  return (
    <div>
      <h1>Utilisateurs</h1>
      <Information_dev type="info" featureName="Utilisateurs" />
      <ThemeSelector />
    </div>
  );
};

export default Users;
