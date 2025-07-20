import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import {
  createComment,
  fetchComments,
  fetchTicketById,
} from "../services/apiService";
import {
  TicketCommentForm,
  TicketCommentList,
} from "../components/TicketComment";

interface Ticket {
  id: number;
  titre: string;
  categorie: string;
  date: string;
  description?: string;
  [key: string]: any;
}

interface Comment {
  id: number;
  auteur: string;
  contenu: string;
  date: string;
  timestamp: number;
}

const TicketPage: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const [ticket, setTicket] = useState<Ticket | null>(null);
  const [comments, setComments] = useState<Comment[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!id) {
      console.warn("Aucun ID de ticket fourni dans l'URL.");
      return;
    }

    loadTicketAndComments();
  }, [id]);

  const loadTicketAndComments = async () => {
    try {
      setLoading(true);
      const ticketId = parseInt(id!, 10);
      console.log("Chargement du ticket ID :", ticketId);

      const t = await fetchTicketById(ticketId);
      console.log("Ticket récupéré :", t);
      setTicket(t.Ticket);

      await reloadComments(ticketId);
    } catch (err: any) {
      console.error("Erreur lors du chargement :", err);
      setError(err.message || "Erreur inconnue");
    } finally {
      setLoading(false);
    }
  };

  const reloadComments = async (ticketId: number) => {
    const c = await fetchComments(ticketId);
    console.log("Commentaires récupérés :", c);

    const commentairesFormates = Array.isArray(c.Commentaires)
      ? c.Commentaires.map((com) => {
          // Construit un objet Date valide à partir de la date et l'heure reçues
          const [jour, mois, annee] = com.date.split("/");
          const timestamp = new Date(
            `${annee}-${mois}-${jour}T${com.heure}`
          ).getTime();

          return {
            id: com.id,
            auteur: `Utilisateur #${com.user_id}`,
            contenu: com.commentaire,
            date: `${com.date} ${com.heure}`,
            timestamp,
          };
        })
      : [];

    // Trie les commentaires
    const commentairesTries = commentairesFormates.sort(
      (a, b) => b.timestamp - a.timestamp
    );

    setComments(commentairesTries);
  };

  const handleNewComment = async (contenu: string) => {
    if (!ticket) return;

    try {
      await createComment(ticket.id, {
        message: contenu,
      });
      await reloadComments(ticket.id);
    } catch (err) {
      console.error("Erreur lors de l'ajout du commentaire :", err);
    }
  };

  if (loading)
    return (
      <div className="TicketPage">
        <p>Chargement...</p>
      </div>
    );
  if (error)
    return (
      <div className="TicketPage">
        <p>Erreur : {error}</p>
      </div>
    );
  if (!ticket)
    return (
      <div className="TicketPage">
        <p>Ticket introuvable</p>
      </div>
    );

  return (
    <div className="TicketPage">
      <div className="ticket-details">
        <h1>
          Ticket #{ticket.id} : {ticket.titre}
        </h1>
        <p>
          <strong>Catégorie :</strong> {ticket.categorie}
        </p>
        <p>
          <strong>Date :</strong> {ticket.date}
        </p>
        {ticket.description && (
          <p>
            <strong>Description :</strong> {ticket.description}
          </p>
        )}
      </div>

      <div className="comments-section">
        <h2>Commentaires</h2>
        <TicketCommentForm onSubmit={handleNewComment} />
        <TicketCommentList comments={comments} />
      </div>
    </div>
  );
};

export default TicketPage;
