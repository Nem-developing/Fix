import { Routes } from '@angular/router';

import { LoginComponent } from '../pages/Auth/Login';
import { RegisterComponent } from '../pages/Auth/Register';
import { ResetPasswordComponent } from '../pages/Auth/ResetPassword';
import { ForgetPasswordComponent } from '../pages/Auth/ChangePassword';
import { LogoutComponent } from '../pages/Auth/Logout';
import { AdminHomeComponent } from '../pages/Admin/Home';


export const routes: Routes = [
  { path: '', component: LoginComponent },
  { path: 'login', component: LoginComponent },
  { path: 'register', component: RegisterComponent },
  { path: 'forgot-password', component: ForgetPasswordComponent },
  { path: 'reset-password/:token', component: ResetPasswordComponent },

  // {
  //   path: 'admin/home',
  //   component: AdminHomeComponent,
  //   canActivate: [AuthGuard], // Example of route protection
  // },
  { path: 'admin/home', component: AdminHomeComponent },
  { path: 'logout', component: LogoutComponent },

  // { path: 'admin/ticket/list', component: AdminTicketListComponent },
  // { path: 'admin/ticket/create', component: CreateTicketComponent },
  // { path: 'admin/ticket/:ticketId', component: ViewTicketComponent },

  // { path: '**', component: NotFoundComponent }, // Wildcard route for a 404 page
];
