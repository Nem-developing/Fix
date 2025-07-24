import React from "react";
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
import Information_dev from "../autres/informationdev";

const CACHE_KEY = "graph-tickets-etat";

export default function EtatTicketGraph() {
  const [data, setData] = React.useState<any[]>([]);
  const [error, setError] = React.useState<string | null>(null);

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

  if (error) return <p>Erreur : {error}</p>;

  return (
    <div>
      {data.length === 0 ? (
        <p></p>
      ) : (
        <ResponsiveContainer width="100%" height={300}>
          <LineChart data={data}>
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
            />
            <Line
              type="monotone"
              dataKey="open"
              stroke="rgb(68, 89, 164)"
              name="Ouverts"
            />
            <Line
              type="monotone"
              dataKey="closed"
              stroke="rgb(52, 149, 140)"
              name="Fermés"
            />
          </LineChart>
        </ResponsiveContainer>
      )}
    </div>
  );
}
