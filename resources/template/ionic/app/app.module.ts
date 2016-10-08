import { NgModule } from '@angular/core';
import { IonicApp, IonicModule } from 'ionic-angular';
import { MyApp } from './app.component';

// import services
// end import services

// import pages
// end import pages

@NgModule({
  declarations: [
    MyApp,
    /* import pages */
  ],
  imports: [
    IonicModule.forRoot(MyApp)
  ],
  bootstrap: [IonicApp],
  entryComponents: [
    MyApp,
    /* import pages */
  ],
  providers: [
    /* import services */
  ]
})
export class AppModule {}
