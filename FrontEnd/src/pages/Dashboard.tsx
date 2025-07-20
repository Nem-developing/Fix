import React from "react";
import DumpNombreTicket from "../components/tickets/DumpNombreTicket";
import EtatTicket from "../components/graph/Etat";

const Dashboard = () => {
  return (
    <div className="Dashboard">
      <h1>Tableau de bord</h1>

      <div className="block">
        <div className="Info-rapide">
          <div className="t-ouvert">
            <h2>OUVERTS</h2>
            <DumpNombreTicket statut="open" result="light" />
          </div>
          <div className="t-encours">
            <h2>EN COURS</h2>
            <DumpNombreTicket statut="inprogress" result="light" />
          </div>
          <div className="t-ferme">
            <h2>FERMÉS</h2>
            <DumpNombreTicket statut="closed" result="light" />
          </div>
        </div>
      </div>
      <div className="block">
        <div className="Graphiques">
          <div className="etat">
            <h2>État des tickets</h2>
            <EtatTicket />
          </div>
          <div className="categorie">
            <h2>Tickets par categorie</h2>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Dashboard;
