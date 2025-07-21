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

export default function EtatTicketGraph() {
  const [data, setData] = React.useState<any[]>([]);
  const [loading, setLoading] = React.useState(true);
  const [error, setError] = React.useState<string | null>(null);

  React.useEffect(() => {
    async function loadData() {
      try {
        const rawData = await fetchGraphData();
        const formattedData = Object.values(rawData).sort(
          (a, b) => new Date(a.date).getTime() - new Date(b.date).getTime()
        );
        setData(formattedData);
      } catch (err: any) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    }

    loadData();
  }, []);

  if (loading) return <p>Chargement du graphique…</p>;
  if (error) return <p>Erreur : {error}</p>;

  return (
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
  );
}
