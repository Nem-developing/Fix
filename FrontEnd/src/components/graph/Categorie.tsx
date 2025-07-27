import React from "react";
import {
  PieChart,
  Pie,
  Cell,
  Tooltip,
  ResponsiveContainer,
  Legend,
} from "recharts";
import { fetchStatsData } from "../../services/apiService";

const CACHE_KEY = "graph-tickets-categorie";

const COLORS = [
  "#f51c1cff",
  "#15c2f7ff",
  "#e9971dff",
  "#c41690ff",
  "#1df81dff",
  "#b9b9b9ff",
];

export default function CategoryTicketGraph() {
  const [data, setData] = React.useState<any[]>([]);
  const [error, setError] = React.useState<string | null>(null);

  React.useEffect(() => {
    // 1. Lecture cache
    try {
      const cached = localStorage.getItem(CACHE_KEY);
      console.log("[CACHE] Donnée brute :", cached);
      if (cached) {
        try {
          const parsed = JSON.parse(cached);
          console.log("[CACHE] Donnée parsée :", parsed);
          if (parsed.data && Array.isArray(parsed.data)) {
            setData(parsed.data);
            console.log("[CACHE] Donnée utilisée pour affichage !");
          }
        } catch (parseError) {
          console.warn("[CACHE] Erreur de parsing JSON:", parseError);
          localStorage.removeItem(CACHE_KEY);
        }
      }
    } catch (e) {
      console.warn("[CACHE] Erreur lecture cache :", e);
    }

    // 2. Appel API
    const fetchFresh = async () => {
      try {
        const rawData = await fetchStatsData();
        console.log("[API] Donnée brute :", rawData);

        const categoriesObj = rawData?.categories?.categories || {};
        console.log("[API] Categories extraites :", categoriesObj);

        const formattedData = Object.entries(categoriesObj).map(
          ([name, value]) => ({ name, value })
        );
        console.log("[API] Données formatées pour graphique :", formattedData);

        setData(formattedData);
        localStorage.setItem(
          CACHE_KEY,
          JSON.stringify({ data: formattedData })
        );
        console.log("[CACHE] Mise à jour avec données API !");
      } catch (err: any) {
        console.error("[API] Erreur lors de l'appel :", err);
        setError(err.message || "Erreur lors du chargement des données");
      }
    };

    fetchFresh();
  }, []);

  if (error) return <p>Erreur : {error}</p>;

  return (
    <ResponsiveContainer width="100%" height={300}>
      <PieChart>
        <Pie
          data={data}
          dataKey="value"
          nameKey="name"
          cx="50%"
          cy="50%"
          outerRadius={90}
          label
        >
          {data.map((entry, index) => (
            <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
          ))}
        </Pie>
        <Tooltip />
        <Legend />
      </PieChart>
    </ResponsiveContainer>
  );
}
