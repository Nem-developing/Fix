// Composant pour la saisie de donnée dans un formulaire (tel que celui de la connexion)

import React from "react";

interface InputFieldProps {
  id: string;
  name: string;
  label: string;
  type: "text" | "password" | "email" | "number" | "textarea";
  value: string | number;
  onChange: (
    e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>
  ) => void;
  required?: boolean;
  error?: string;
  maxLength?: number;
}

const InputField: React.FC<InputFieldProps> = ({
  id,
  name,
  label,
  type,
  value,
  onChange,
  required = false,
  error,
  maxLength,
}) => {
  const InputComponent = type === "textarea" ? "textarea" : "input";

  return (
    <div className="form-group">
      <label htmlFor={id} className="form-label">
        {label} :
      </label>
      <InputComponent
        id={id}
        name={name}
        className="form-input"
        value={value}
        onChange={onChange}
        required={required}
        {...(type !== "textarea" && { type })}
        autoComplete="off"
        {...(maxLength ? { maxLength } : {})}
      />
      {error && <p className="form-error">{error}</p>}
    </div>
  );
};

export default InputField;
