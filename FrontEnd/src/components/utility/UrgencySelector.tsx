import React from "react";

interface UrgencySelectorProps {
  selectedUrgency: number;
  onSelectUrgency: (urgency: number) => void;
}

const UrgencySelector: React.FC<UrgencySelectorProps> = ({
  selectedUrgency,
  onSelectUrgency,
}) => {
  return (
    <div>
      <label>Urgence :</label>
      <select
        name="urgence"
        value={selectedUrgency}
        onChange={(e) => onSelectUrgency(parseInt(e.target.value, 10))}
      >
        <option value={0}>Faible</option>
        <option value={1}>Moyenne</option>
        <option value={2}>Élevée</option>
      </select>
    </div>
  );
};

export default UrgencySelector;
