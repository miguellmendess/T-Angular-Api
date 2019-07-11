import {RouterModule, Routes} from '@angular/router';
import {HomeComponent} from './home/home.component';
import {LoginComponent} from './login/login.component';
import {CheckinComponent} from './checkin/checkin.component';
import {CheckoutComponent} from './checkout/checkout.component';
import {ModuleWithProviders} from '@angular/core';

const APP_ROUTES: Routes=[
  { path: '', component: HomeComponent },
  { path: 'login', component: LoginComponent},
  { path: 'checkin', component: CheckinComponent},
  { path: 'checkout', component: CheckoutComponent }
];

export const routing: ModuleWithProviders = RouterModule.forRoot(APP_ROUTES);
