import React, { useState, useEffect } from "react";

interface Comment {
  id: number;
  auteur: string;
  contenu: string;
  date: string;
}

interface TicketCommentListProps {
  comments: Comment[];
  onEdit?: (comment: Comment) => Promise<void>;
  onDelete?: (commentId: number) => void;
}

interface CommentFormProps {
  initialComment?: string;
  onSubmit: (contenu: string) => void;
  onCancel?: () => void;
}

export const TicketCommentList: React.FC<TicketCommentListProps> = ({
  comments,
  onEdit,
  onDelete,
}) => {
  return (
    <div className="ticket-comment-list">
      <h3>Commentaires ({comments.length})</h3>
      <ul>
        {comments.map((comment) => (
          <li key={comment.id} className="comment-item">
            <div className="comment-header">
              <strong>{comment.auteur}</strong>
              <span className="comment-date">{comment.date}</span>
            </div>
            <p>{comment.contenu}</p>
            <div className="comment-actions">
              <button onClick={() => onEdit?.(comment)}> Modifier</button>
              <button onClick={() => onDelete?.(comment.id)}>Supprimer</button>
            </div>
          </li>
        ))}
      </ul>
    </div>
  );
};

export const TicketCommentForm: React.FC<CommentFormProps> = ({
  initialComment = "",
  onSubmit,
  onCancel,
}) => {
  const [contenu, setContenu] = useState(initialComment);

  // Met à jour le contenu quand initialComment change (pour le mode édition)
  useEffect(() => {
    setContenu(initialComment);
  }, [initialComment]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (contenu.trim()) {
      onSubmit(contenu.trim());
      setContenu("");
    }
  };

  return (
    <form className="ticket-comment-form" onSubmit={handleSubmit}>
      <textarea
        value={contenu}
        onChange={(e) => setContenu(e.target.value)}
        placeholder="Ajouter un commentaire..."
        required
      />
      <div className="form-buttons">
        <button type="submit"> Envoyer</button>
        {onCancel && (
          <button type="button" onClick={onCancel}>
            Annuler
          </button>
        )}
      </div>
    </form>
  );
};
