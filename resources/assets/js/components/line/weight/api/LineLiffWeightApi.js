export class LineLiffWeightApi {

  constructor(domain, userId) {
    this._userId = userId;
    this._baseUrl = `${domain}/line/liff/weight`;
  }

  getSetting() {
    return api.get(`${this._baseUrl}/setting/${this._userId}`);
  }

  getWeightsRecords() {
    return api.get(`${this._baseUrl}/records/all/${this._userId}`);
  }

  getWeeklyWeightsRecords() {
    return api.get(`${this._baseUrl}/records/weekly/${this._userId}`);
  }
}
