// Composant pour la saisie de donnée dans un formulaire (tel que celui de la connexion)

import React from "react";

interface InputFieldProps {
  id: string;
  label: string;
  type: string;
  value: string;
  onChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
  required?: boolean;
}

const InputField: React.FC<InputFieldProps> = ({
  id,
  label,
  type,
  value,
  onChange,
  required = false,
}) => {
  return (
    <div className="form-group">
      <label htmlFor={id} className="form-label">
        {label} :F
      </label>
      <input
        type={type}
        id={id}
        className="form-input"
        value={value}
        onChange={onChange}
        required={required}
      />
    </div>
  );
};

export default InputField;
