import React, { useEffect, useState, useMemo } from "react";
import { fetchTickets } from "../../services/apiService";

interface DumpNombreTicketProps {
  statut?: string; // Filtrage par statut (ex: "open", "closed")
  format?: "table"; // Affichage sous forme de tableau si présent
  data?: string | string[]; // Colonnes à afficher
  exclude?: string | string[]; // Colonnes à exclure
  result?: "light"; // Mode "light" pour retourner juste un nombre
  extraWarningColumn?: boolean; // Ajoute une colonne "avertissement" si activé
  onTicketClick?: (ticket: any) => void; // Permet l'ouverture d'une popup de modification
}

const statutMapping: Record<string, number> = {
  open: 0,
  inprogress: 1,
  blocked: 2,
  test: 3,
  review: 4,
  closed: 5,
};

// Cache global pour éviter les appels multiples
class TicketCache {
  private static instance: TicketCache;
  private cacheKey = "tickets-global-cache";
  private lastFetchKey = "tickets-last-fetch";
  private data: any[] = [];
  private listeners: Set<() => void> = new Set();
  private isLoading = false;
  private CACHE_DURATION = 5 * 60 * 1000; // 5 minutes
  private AUTO_REFRESH_INTERVAL = 2 * 60 * 1000; // 2 minutes
  private intervalId: number | null = null;
  private lastDataHash: string = "";

  static getInstance(): TicketCache {
    if (!TicketCache.instance) {
      TicketCache.instance = new TicketCache();
    }
    return TicketCache.instance;
  }

  private constructor() {
    this.loadFromStorage();
    this.lastDataHash = this.generateDataHash(this.data);
    this.startAutoRefresh();
  }

  private generateDataHash(data: any[]): string {
    // Génère un hash simple basé sur la longueur et quelques propriétés clés
    if (!data.length) return "0";

    const firstItem = data[0];
    const lastItem = data[data.length - 1];
    const middleItem = data[Math.floor(data.length / 2)];

    return `${data.length}-${JSON.stringify(
      firstItem?.id || ""
    )}-${JSON.stringify(lastItem?.id || "")}-${JSON.stringify(
      middleItem?.id || ""
    )}`;
  }

  private hasDataChanged(newData: any[]): boolean {
    const newHash = this.generateDataHash(newData);
    const hasChanged = newHash !== this.lastDataHash;
    this.lastDataHash = newHash;
    return hasChanged;
  }

  private loadFromStorage() {
    try {
      const cached = localStorage.getItem(this.cacheKey);
      if (cached) {
        this.data = JSON.parse(cached);
      }
    } catch {
      this.data = [];
    }
  }

  private saveToStorage() {
    try {
      localStorage.setItem(this.cacheKey, JSON.stringify(this.data));
      localStorage.setItem(this.lastFetchKey, Date.now().toString());
    } catch (error) {
      console.warn("Erreur lors de la sauvegarde en localStorage:", error);
    }
  }

  private isCacheValid(): boolean {
    try {
      const lastFetch = localStorage.getItem(this.lastFetchKey);
      if (!lastFetch) return false;

      const lastFetchTime = parseInt(lastFetch);
      return Date.now() - lastFetchTime < this.CACHE_DURATION;
    } catch {
      return false;
    }
  }

  private startAutoRefresh() {
    this.intervalId = window.setInterval(() => {
      this.refreshData();
    }, this.AUTO_REFRESH_INTERVAL);
  }

  subscribe(callback: () => void) {
    this.listeners.add(callback);
    return () => this.listeners.delete(callback);
  }

  private notifyListeners() {
    this.listeners.forEach((callback) => callback());
  }

  getData(): any[] {
    return this.data;
  }

  isDataLoading(): boolean {
    return this.isLoading;
  }

  async refreshData(force = false) {
    if (this.isLoading && !force) return;

    // Si on a des données en cache et qu'elles sont valides, on les retourne immédiatement
    if (!force && this.data.length > 0 && this.isCacheValid()) {
      return;
    }

    this.isLoading = true;
    this.notifyListeners();

    try {
      const newData = await fetchTickets("tickets");

      // Ne mettre à jour que si les données ont vraiment changé
      if (this.hasDataChanged(newData)) {
        this.data = newData;
        this.saveToStorage();
        this.notifyListeners();
      }
    } catch (error) {
      console.error("Erreur lors du chargement des tickets:", error);
    } finally {
      this.isLoading = false;
      this.notifyListeners();
    }
  }

  destroy() {
    if (this.intervalId) {
      window.clearInterval(this.intervalId);
      this.intervalId = null;
    }
    this.listeners.clear();
  }
}

const DumpNombreTicket: React.FC<DumpNombreTicketProps> = ({
  statut,
  format,
  data,
  exclude,
  result,
  extraWarningColumn,
  onTicketClick,
}) => {
  const cache = TicketCache.getInstance();
  const [, forceUpdate] = useState({});

  // S'abonner aux changements du cache
  useEffect(() => {
    const unsubscribe = cache.subscribe(() => {
      forceUpdate({});
    });

    // Charger les données au montage si nécessaire
    cache.refreshData();

    return () => {
      unsubscribe();
    };
  }, [cache]);

  // Calculer les tickets filtrés côté client
  const filteredTickets = useMemo(() => {
    const allTickets = cache.getData();

    if (!statut) return allTickets;

    const statutNum = statutMapping[statut];
    if (statutNum === undefined) return allTickets;

    return allTickets.filter((ticket: any) => ticket.statut === statutNum);
  }, [cache.getData(), statut]);

  const isLoading = cache.isDataLoading();
  const hasInitialData = cache.getData().length > 0;

  // Affichage du loading uniquement si on n'a aucune donnée
  if (isLoading && !hasInitialData) {
    return <p>Chargement...</p>;
  }

  // Mode "light" - retourne juste le nombre
  if (result === "light") {
    return (
      <div className="nb-DumpNombreTicket">
        {filteredTickets.length}
        {isLoading && hasInitialData && (
          <span style={{ opacity: 0.6, fontSize: "0.8em", marginLeft: "4px" }}>
            ↻
          </span>
        )}
      </div>
    );
  }

  // Mode tableau
  if (format === "table") {
    const sample = filteredTickets[0] || {};
    let columns = Object.keys(sample);

    if (data) {
      if (typeof data === "string") {
        columns = data.split(",").map((s) => s.trim());
      } else {
        columns = data;
      }
    } else if (exclude) {
      const excludeList = Array.isArray(exclude) ? exclude : [exclude];
      columns = columns.filter((col) => !excludeList.includes(col));
    }

    // Ajoute dynamiquement la colonne "avertissement" si activé
    const finalColumns = extraWarningColumn ? [...columns, "il y a"] : columns;

    return (
      <div>
        {isLoading && hasInitialData && <div>Mise à jour...</div>}
        <table className="DumpNombreTicket">
          <thead>
            <tr>
              {finalColumns.map((key) => (
                <th key={key}>{key}</th>
              ))}
            </tr>
          </thead>
          <tbody>
            {filteredTickets.map((ticket, i) => {
              let daysLate: number | null = null;
              let isLate = false;

              if (extraWarningColumn && ticket.date) {
                const parts = ticket.date.split("/");
                if (parts.length === 3) {
                  // Format DD/MM/YYYY -> YYYY-MM-DD
                  const isoDate = `${parts[2]}-${parts[1].padStart(
                    2,
                    "0"
                  )}-${parts[0].padStart(2, "0")}`;
                  const ticketDate = new Date(isoDate);
                  if (!isNaN(ticketDate.getTime())) {
                    const now = new Date();
                    const diffTime = now.getTime() - ticketDate.getTime();
                    daysLate = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                    // Nombre de jours pour l'avertissement
                    isLate = daysLate >= 7;
                  }
                }
              }

              return (
                <tr
                  key={i}
                  onClick={() => onTicketClick?.(ticket)}
                  className={onTicketClick ? "clickable-row" : ""}
                >
                  {columns.map((key) => (
                    <td key={key}>{String(ticket[key] || "")}</td>
                  ))}
                  {extraWarningColumn && (
                    <td key="avertissement">
                      {daysLate !== null
                        ? `${daysLate} jours${isLate ? " ❗" : ""}`
                        : "-"}
                    </td>
                  )}
                </tr>
              );
            })}
          </tbody>
        </table>
      </div>
    );
  }

  // Mode par défaut (sans format)
  return (
    <div className="DumpNombreTicket">
      {filteredTickets.length} tickets
      {isLoading && hasInitialData && <span> (mise à jour...)</span>}
    </div>
  );
};

export default DumpNombreTicket;
