export class LineLiffMealApi {

  constructor(domain, userId) {
    this._userId = userId;
    this._baseUrl = `${domain}/line/liff/meal`;
  }

  getSetting() {
    return api.get(`${this._baseUrl}/setting/${this._userId}`);
  }

  getMealTypes() {
    return api.get(`${this._baseUrl}/meal_types`);
  }

  getMealRecords() {
    return api.get(`${this._baseUrl}/records/all/${this._userId}`);
  }

  getWeeklyWeightsRecords() {
    return api.get(`${this._baseUrl}/records/weekly/${this._userId}`);
  }
}
