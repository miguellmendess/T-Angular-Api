import {Component, ElementRef, OnInit, ViewChild} from '@angular/core';
import {CheckinService} from '../checkin.service';

@Component({
  selector: 'app-checkout',
  templateUrl: './checkout.component.html',
  styleUrls: ['./checkout.component.css']
})
export class CheckoutComponent implements OnInit {
  @ViewChild('cardRFID', {static: false}) cardRFID:ElementRef;
  dataCard : any[];
  haveTicket = false;
  searchTicket = false;

  constructor(private checkinService: CheckinService) { }

  ngOnInit() {
    this.focusCard();
  }

  focusCard(){
    if(this.cardRFID)this.cardRFID.nativeElement.focus();
  }

  readCard(){
    this.searchTicket = true;
    let value = this.cardRFID.nativeElement.value;
    if(value.length>9){
      this.dataCard = ['1','3'];
      let parent = this;
      this.checkinService.validateCheckout(3,value).subscribe(data=>{
        parent.dataCard = data['result'];
        parent.haveTicket = data['checkTicket'];
        console.log(parent.haveTicket);
        console.log(parent.dataCard);
      });
      console.log(this.dataCard);
      this.cardRFID.nativeElement.value = '';
      this.searchTicket = true;
    }
    //
  }

  calcValueTotal(value:number, qtd:number){
    let valueTotal = (value * qtd);
    return valueTotal;
  }

}
