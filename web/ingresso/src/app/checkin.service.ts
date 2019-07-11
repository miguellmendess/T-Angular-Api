import { Injectable } from '@angular/core';
import {HttpClient} from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class CheckinService {

  constructor(private http: HttpClient){}
  validateCheckIn(idEvent:number, numberCard:number){

    let urlC = 'http://localhost:80/checkin?type=checkin&idEvent='+idEvent+'&numberCard='+numberCard;

    return this.http.get<any[]>(`${urlC}`);

  }

  validateCheckout(idEvent:number, numberCard:number){

    let urlC = 'http://localhost:80/checkin?type=checkout&idEvent='+idEvent+'&numberCard='+numberCard;

    return this.http.get<any[]>(`${urlC}`);

  }
}
