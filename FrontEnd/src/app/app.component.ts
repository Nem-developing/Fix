import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { HeaderbarComponent } from './headerbar/headerbar.component';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet,HeaderbarComponent],
  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})
export class AppComponent {
  title = 'FrontEnd';
}
