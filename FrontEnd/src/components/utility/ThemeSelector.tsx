import { useTheme } from "../../context/useTheme";

const ThemeSelector = () => {
  const { theme, setTheme } = useTheme();

  return (
    <div>
      <label>Thème :</label>
      <select
        value={theme}
        onChange={(e) => setTheme(e.target.value as "light" | "dark")}
      >
        <option value="light">Clair</option>
        <option value="dark">Sombre</option>
      </select>
    </div>
  );
};

export default ThemeSelector;
