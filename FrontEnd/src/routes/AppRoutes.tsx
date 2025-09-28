import React from "react";
import { Routes, Route, Navigate } from "react-router-dom";
import Dashboard from "../pages/Dashboard";
import NewTicket from "../pages/NewTicket";
import OpenTickets from "../pages/OpenTickets";
import TicketHistory from "../pages/TicketHistory";
import Reports from "../pages/Reports";
import Users from "../pages/Users";
import Ticket from "../pages/Ticket";
import TicketPage from "../pages/TicketPage";
import Connection from "../pages/Connection";

const AppRoutes = () => {
  return (
    <Routes>
      <Route path="/" element={<Navigate to="/dashboard" />} />
      <Route path="/dashboard" element={<Dashboard />} />
      <Route path="/new" element={<NewTicket />} />
      <Route path="/open" element={<OpenTickets />} />
      <Route path="/history" element={<TicketHistory />} />
      <Route path="ticket" element={<Ticket />} />
      <Route path="/reports" element={<Reports />} />
      <Route path="/users" element={<Users />} />
      <Route path="/ticket/:id" element={<TicketPage />} />
      <Route path="/connection" element={<Connection />} />
    </Routes>
  );
};

export default AppRoutes;
