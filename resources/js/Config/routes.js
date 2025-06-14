export default {

  // главная
  home: () => '/',

  // профиль пользователя
  profile: function (id) {
    return `/users/${id}/edit`;
  },

  // управление пользователями
  manageUsers: () => '/users',

  // выход
  logout: () => '/logout',

  // организации
  organizations: () => '/organizations',

  // табель отпусков
  table: () => '/table',

  // отпуска в виде структуры
  structure: () => '/structure',

  // поиск
  find: () => '/find',



  // статистика для главной страницы
  statisticsTotalEmployees: function (orgCode, year) {
    return `/statistics/total-employees/${orgCode}/${year}`;
  },

  // список сотрудников в отпуске
  employeesOnVacations: function (orgCode, year) {
    return `/vacations/employees-on-vacations/${orgCode}/${year}`;
  },

  // список идущие в отпуск
  employeesWillBeOnVacations: function (orgCode, year) {
    return `/vacations/employees-will-be-on-vacations/${orgCode}/${year}`;
  },

  // данные для графика на главной странице
  chartCountOfVacationsPerYearByDay: function (orgCode, year) {
    return `/chart/count-of-vacations-per-year-by-day/${orgCode}/${year}`;
  },

  // данные об отпуске по каждому сотруднику
  tableData: function (org_code, year) {
    return `/table/table-data/${org_code}/${year}`;
  },

  vacationsFind: function (org_code, year) {
    return `/find-data/${org_code}/${year}`;
  },

  // данные по отпускам по структуре организации
  structureData: function(org_code, year) {
    return `${this.structure()}/data/${org_code}/${year}`;
  },

  // журнал импорта
  loadHistory: () => '/load-history',
  

}