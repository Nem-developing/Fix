import React from "react";

interface InformationDevProps {
  type?: "info" | "warning" | "error" | "success"; // Type d'information (pour le style et le message par défaut)
  message?: string; // Message personnalisé
  featureName?: string; // Nom de la fonctionnalité (pour les messages par défaut)
  size?: "small" | "medium" | "large"; // Taille de l'affichage
}

const Information_dev: React.FC<InformationDevProps> = ({
  type = "info",
  message,
  featureName,
  size = "medium",
}) => {
  // Définition des messages par défaut en fonction du type et du featureName
  const defaultMessages = {
    info: featureName
      ? `La fonctionnalité "${featureName}" est en cours de développement.`
      : "Information de développement.",
    warning: featureName
      ? `Attention : L'endpoint pour "${featureName}" n'est pas encore implémenté.`
      : "Attention : Ce composant est en cours de développement.",
    error: featureName
      ? `Erreur de développement : Impossible de charger "${featureName}".`
      : "Erreur : Problème de configuration du composant de développement.",
    success: featureName
      ? `La fonctionnalité "${featureName}" semble opérationnelle en développement.`
      : "Développement réussi pour ce composant.",
  };

  // Choix du message final : personnalisé ou par défaut
  const displayMessage = message || defaultMessages[type];

  // Classes CSS pour le style et la taille
  const baseClassName = "information-dev";
  const typeClassName = `${baseClassName}--${type}`;
  const sizeClassName = `${baseClassName}--${size}`;

  return (
    <div className={`${baseClassName} ${typeClassName} ${sizeClassName}`}>
      <span className={`${baseClassName}__icon`}>
        {type === "info" && "ℹ️"}
        {type === "warning" && "⚠️"}
        {type === "error" && "❌"}
        {type === "success" && "✅"}
      </span>
      <p className={`${baseClassName}__message`}>{displayMessage}</p>
    </div>
  );
};

export default Information_dev;
