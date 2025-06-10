import React from "react";
import DumpNombreTicket from "../components/DumpNombreTicket";

const Dashboard = () => {
  return (
    <div className="Dashboard">
      <h1>Tableau de bord</h1>

      <div className="block">
        <div className="Info-rapide">
          <div className="t-ouvert">
            <h2>OUVERTS</h2>
            <DumpNombreTicket statut="open" />
          </div>
          <div className="t-encours">
            <h2>EN COURS</h2>
            <DumpNombreTicket statut="inprogress" />
          </div>
          <div className="t-ferme">
            <h2>FERMÉS</h2>
            <DumpNombreTicket statut="closed" />
          </div>
        </div>
      </div>
      <div className="block">
        <div className="Graphiques">
          <div className="etat"></div>
          <div className="categorie"></div>
        </div>
      </div>
    </div>
  );
};

export default Dashboard;
