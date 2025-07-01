import React, { useEffect, useState } from "react";

const Navbar = () => {
  const [UserName, setUserName] = useState("...");

  useEffect(() => {
    fetch("/data/users.json")
      .then((res) => {
        // console.log("Réponse fetch:", res);
        if (!res.ok) {
          throw new Error("Erreur HTTP " + res.status);
        }
        return res.json();
      })
      .then((data) => {
        // console.log("Données reçues:", data);
        if (data.length > 0) {
          setUserName(data[0].name || data[0].username || "Utilisateur");
        } else {
          setUserName("Utilisateur");
        }
      })
      .catch((err) => {
        // console.error("Erreur fetch :", err);
        setUserName("Utilisateur");
      });
  }, []);

  return <header></header>;
};

export default Navbar;
