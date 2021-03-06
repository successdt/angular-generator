import {Injectable} from "@angular/core";
import {{SERVICE_NAMES}} from "./mock-{service-names}";

@Injectable()
export class {ServiceName}Service {
  private {serviceNames}:any;

  constructor() {
    this.{serviceNames} = {SERVICE_NAMES};
  }

  getAll() {
    return this.{serviceNames};
  }

  getItem(id) {
    for (let i = 0; i < this.{serviceNames}.length; i++) {
      if (this.{serviceNames}[i].id === parseInt(id)) {
        return this.{serviceNames}[i];
      }
    }
    return null;
  }

  remove(item) {
    this.{serviceNames}.splice(this.{serviceNames}.indexOf(item), 1);
  }
}