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
  { name: "Feature", value: 5 },
  { name: "Support", value: 3 },
];

// Couleurs pour chaque catégorie
const COLORS = ["#8884d8", "#82ca9d", "#ffc658"];

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
