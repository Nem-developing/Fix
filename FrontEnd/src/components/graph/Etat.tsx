import React, { useState, useMemo } from "react";
import {
  LineChart,
  Line,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  Legend,
  ResponsiveContainer,
} from "recharts";
import { fetchGraphData } from "../../services/apiService";
// import Information_dev from "../autres/informationdev";

const CACHE_KEY = "graph-tickets-etat";

export default function EtatTicketGraph() {
  const [data, setData] = React.useState<any[]>([]);
  const [error, setError] = React.useState<string | null>(null);

  // Par defaut on affiche les 7 derniers jours
  const [filterType, setFilterType] = useState<"7d" | "30d" | "month">("7d");
  const [selectedMonth, setSelectedMonth] = useState(
    new Date().toISOString().slice(0, 7)
  );

  React.useEffect(() => {
    // 1. Affichage immédiat depuis cache
    try {
      const cached = localStorage.getItem(CACHE_KEY);
      if (cached) {
        const parsed = JSON.parse(cached);
        if (parsed.data && parsed.timestamp) {
          setData(parsed.data);
        }
      }
    } catch (e) {
      console.warn("Erreur lecture cache :", e);
    }

    // 2. Appel API en arrière-plan
    const fetchFresh = async () => {
      try {
        const rawData = await fetchGraphData();
        const formattedData = Object.values(rawData).sort(
          (a, b) => new Date(a.date).getTime() - new Date(b.date).getTime()
        );
        setData(formattedData);
        localStorage.setItem(
          CACHE_KEY,
          JSON.stringify({ timestamp: Date.now(), data: formattedData })
        );
      } catch (err: any) {
        setError(err.message);
      }
    };

    fetchFresh();
  }, []);

  const filteredData = useMemo(() => {
    // Si pas de données brutes, on peut quand même vouloir afficher la grille vide
    const dataMap = new Map();
    if (data) {
      data.forEach((item) => {
        dataMap.set(item.date, item);
      });
    }

    const result: any[] = [];
    const today = new Date();

    // Fonction utilitaire pour formater YYYY-MM-DD en local pour éviter les décalages UTC
    const toDateString = (date: Date) => {
      const offset = date.getTimezoneOffset();
      const localDate = new Date(date.getTime() - offset * 60 * 1000);
      return localDate.toISOString().split("T")[0];
    };

    if (filterType === "7d") {
      for (let i = 6; i >= 0; i--) {
        const d = new Date();
        d.setDate(today.getDate() - i);
        const dateStr = toDateString(d);
        result.push(
          dataMap.get(dateStr) || { date: dateStr, new: 0, open: 0, closed: 0 }
        );
      }
    } else if (filterType === "30d") {
      for (let i = 29; i >= 0; i--) {
        const d = new Date();
        d.setDate(today.getDate() - i);
        const dateStr = toDateString(d);
        result.push(
          dataMap.get(dateStr) || { date: dateStr, new: 0, open: 0, closed: 0 }
        );
      }
    } else if (filterType === "month") {
      // selectedMonth est sous forme "YYYY-MM"
      const [year, month] = selectedMonth.split("-").map(Number);
      // Le jour 0 du mois suivant nous donne le dernier jour du mois actuel
      const daysInMonth = new Date(year, month, 0).getDate();

      for (let i = 1; i <= daysInMonth; i++) {
        const d = new Date(year, month - 1, i);
        const dateStr = toDateString(d);
        result.push(
          dataMap.get(dateStr) || { date: dateStr, new: 0, open: 0, closed: 0 }
        );
      }
    }

    return result;
  }, [data, filterType, selectedMonth]);

  if (error) return <p>Erreur : {error}</p>;

  return (
    <div>
      <div className="graph-controls">
        <button
          className={filterType === "7d" ? "active" : ""}
          onClick={() => setFilterType("7d")}
        >
          7 Jours
        </button>
        <button
          className={filterType === "30d" ? "active" : ""}
          onClick={() => setFilterType("30d")}
        >
          30 Jours
        </button>
        <button
          className={filterType === "month" ? "active" : ""}
          onClick={() => setFilterType("month")}
        >
          Mois
        </button>

        {filterType === "month" && (
          <input
            type="month"
            value={selectedMonth}
            onChange={(e) => setSelectedMonth(e.target.value)}
          />
        )}
      </div>

      {filteredData.length === 0 ? (
        <p style={{ textAlign: "center", padding: "20px", color: "#888" }}>
          Aucune donnée disponible pour cette période.
        </p>
      ) : (
        <ResponsiveContainer width="100%" height={300}>
          <LineChart data={filteredData}>
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis dataKey="date" />
            <YAxis
              domain={[0, "dataMax"]}
              tickFormatter={(value) => value}
              allowDecimals={false}
            />
            <Tooltip />
            <Legend />
            <Line
              type="monotone"
              dataKey="new"
              stroke="rgb(71, 142, 230)"
              name="Nouveaux"
              animationDuration={500}
            />
            <Line
              type="monotone"
              dataKey="open"
              stroke="rgb(68, 89, 164)"
              name="Ouverts"
              animationDuration={500}
            />
            <Line
              type="monotone"
              dataKey="closed"
              stroke="rgb(52, 149, 140)"
              name="Fermés"
              animationDuration={500}
            />
          </LineChart>
        </ResponsiveContainer>
      )}
    </div>
  );
}
