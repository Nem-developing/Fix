import React from "react";
import {
  PieChart,
  Pie,
  Cell,
  Tooltip,
  ResponsiveContainer,
  Legend,
} from "recharts";

// Données factices des catégories de tickets
const mockData = [
  { name: "Bug", value: 8 },
  { name: "Demande d'information", value: 5 },
  { name: "Support technique", value: 2 },
  { name: "Problème de compte", value: 9 },
  { name: "Suggestion", value: 1 },
  { name: "Autre", value: 8 },
];

// Couleurs pour chaque catégorie
const COLORS = [
  "#f51c1cff",
  "#15c2f7ff",
  "#e9971dff",
  "#c41690ff",
  "#1df81dff",
  "#b9b9b9ff",
];

export default function CategoryTicketGraph() {
  return (
    <ResponsiveContainer width="100%" height={300}>
      <PieChart>
        <Pie
          data={mockData}
          dataKey="value"
          nameKey="name"
          cx="50%"
          cy="50%"
          outerRadius={90}
          label
        >
          {mockData.map((entry, index) => (
            <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
          ))}
        </Pie>
        <Tooltip />
        <Legend />
      </PieChart>
    </ResponsiveContainer>
  );
}
