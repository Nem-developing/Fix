import React from "react";
import { PieChart, Pie, Cell, Tooltip, ResponsiveContainer } from "recharts";
import { fetchStatsData } from "../../services/apiService";

const CACHE_KEY = "graph-tickets-categorie";

const COLORS = [
  "#ff9b3eff", // Orange Vif
  "#33FF57", // Vert Pomme
  "#3357FF", // Bleu Électrique
  "#FF33F6", // Fuchsia Éclatant
  "#F6FF33", // Jaune Citron
  "#33FFF6", // Cyan Lumineux
  "#FF3333", // Rouge Sang
  "#aa33ffff", // Violet Intense
  "#33FFB5", // Vert Menthe
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
    <div style={{ width: "100%", display: "flex", flexDirection: "column" }}>
      <div style={{ width: "100%", height: 300 }}>
        <ResponsiveContainer>
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
                <Cell
                  key={`cell-${index}`}
                  fill={COLORS[index % COLORS.length]}
                />
              ))}
            </Pie>
            <Tooltip />
          </PieChart>
        </ResponsiveContainer>
      </div>
      <div
        className="custom-legend"
        style={{
          display: "flex",
          flexWrap: "wrap",
          padding: "0 20px 20px 20px",
          gap: "10px",
          justifyContent: "center",
        }}
      >
        {data.map((entry, index) => (
          <div
            key={`legend-${index}`}
            style={{
              display: "flex",
              alignItems: "center",
              fontSize: "0.9rem",
              color: "#555",
            }}
          >
            <span
              style={{
                display: "inline-block",
                width: 12,
                height: 12,
                backgroundColor: COLORS[index % COLORS.length],
                marginRight: 8,
                borderRadius: "2px",
              }}
            ></span>
            {entry.name} ({entry.value})
          </div>
        ))}
      </div>
    </div>
  );
}
