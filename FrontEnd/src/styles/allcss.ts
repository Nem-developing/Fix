/// <reference types="vite/client" />

const cssModules = import.meta.glob("./*.css", { eager: true });

// Nom des fichier css = ordre d'importation
// 1 = premier -> CSS reset pour mettre au propre (ne rien mettre d'autre sauf exception)
// 2 = secondaire -> mettre les élements importants de l'application
// 3 = tertiaire -> le reste du css
// 4 = dernier -> modif de dernière minute ou test
