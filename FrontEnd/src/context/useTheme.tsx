import React, { createContext, useContext, useEffect, useState } from "react";

// Définit les types de thème possibles
type Theme = "light" | "dark";

// Crée le contexte du thème avec une valeur par défaut
const ThemeContext = createContext<{
  theme: Theme;
  setTheme: (theme: Theme) => void;
}>({ theme: "light", setTheme: () => {} });

// Le fournisseur de thème qui gère l'état du thème et sa persistance
export const ThemeProvider: React.FC<{ children: React.ReactNode }> = ({
  children,
}) => {
  // Initialise l'état du thème en essayant de le récupérer depuis le localStorage
  // Si aucun thème n'est trouvé, 'light' est utilisé par défaut.
  const [theme, setThemeState] = useState<Theme>(() => {
    return (localStorage.getItem("theme") as Theme) || "light";
  });

  // Utilise useEffect pour mettre à jour le localStorage et l'attribut 'data-theme' sur le body
  // chaque fois que le thème change.
  // L'importation dynamique des fichiers SCSS a été supprimée car elle n'est pas nécessaire
  // et ne fonctionne pas comme prévu au runtime pour les styles compilés.
  useEffect(() => {
    localStorage.setItem("theme", theme);
    document.body.setAttribute("data-theme", theme);
  }, [theme]); // Dépendance à 'theme' pour réexécuter l'effet lors des changements

  // Fonction pour changer le thème, qui met à jour l'état interne
  const setTheme = (t: Theme) => setThemeState(t);

  return (
    // Fournit le thème actuel et la fonction pour le changer à tous les composants enfants
    <ThemeContext.Provider value={{ theme, setTheme }}>
      {children}
    </ThemeContext.Provider>
  );
};

// Hook personnalisé pour accéder facilement au thème et à la fonction setTheme
export const useTheme = () => useContext(ThemeContext);
