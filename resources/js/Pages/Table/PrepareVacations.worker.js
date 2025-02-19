import moment from "moment";
moment.locale('ru')

let totalEmployees = 0;

self.onmessage = function ({ data }) {    
  const calendar = generateCalendar(data.year);
  let result = [];
  if (Array.isArray(data.data) && data.data.length > 0) {
    result = transform(data.data);
    result = calc(result, calendar);    
  } 
  self.postMessage({ items: result, calendar: calendar, totalEmployees: totalEmployees });
};


// генерирование календаря помесячно
const generateCalendar = (year) => {  

  const dateNow = moment()
  const today = {
    year: dateNow.format('YYYY'),
    month: +dateNow.format('MM'),
  }

  let res = {}
  for (let i = 1; i <= 12; i++) {
    let date = new Date(year, i, 0)
    let monthNumber = date.getMonth() + 1
    res[monthNumber] = {
      name: date.toLocaleDateString('default', { month: 'long' }),
      days: date.getDate(),
      hideDetail: !(year == today.year && i == today.month),
      currentMonth: year == today.year && i == today.month,
      month: moment(date).format('MM'),
      dates: generateCalendarPerDayByMonth(year, monthNumber),
      total: 0,
    }
  } 
  return res
}

const generateCalendarPerDayByMonth = (year, month) => {
  const dateNow = moment()
  let res = []
  let date = moment(new Date(year, month-1, 1));
  while (date.format('M') == (month)) {
    res.push({
      date: date.format('D'),
      dateStr: date.format('L'),
      dayOfWeek: date.format('dd'),
      isWeekend: +date.format('E') >= 6,
      isToday: date.format('YYYY-MM-DD') == dateNow.format('YYYY-MM-DD'),
    })
    date.add(1, 'days');
  }
  return res;
}

// преобразование к древовидной структуре
const transform = (data) => {

  let result = {};  
  data.forEach(item => {
    if (!result[item.id_department]) {
      result[item.id_department] = {
        id: item.id_department,
        name: item.department,
        sort: item.sort_index_department,
        count_employees: 0,
        employees: {},
        hideDetail: true,
        months: {},        
      }
    }
    if (!result[item.id_department]['employees'][item.id_employee]) {
      result[item.id_department]['employees'][item.id_employee] = {
        id: item.id_employee,
        name: item.full_name,
        post: item.post,
        sort: 0,//item.sort_index_employee,
        vacations: [],
        months: {},
      }
      result[item.id_department].count_employees++;
    }
    result[item.id_department]['employees'][item.id_employee].vacations.push({      
      date_from: item.date_from,
      date_to: item.date_to,
      status: item.status,
      id_kind: item.id_kind,
      name: item.vacation_kind,
    })
  })

  // сортировка отделов
  result = sort(result);
  // сортировка сотрудников
  result = sortByEmployees(result);
  return result;

}

// сортировка 
const sort = (data) => {
  return Object.entries(data).sort((a, b) => {
    const sortA = a[1].sort === null ? Infinity : a[1].sort; // Сортировка по sort, null будет считаться как Infinity
    const sortB = b[1].sort === null ? Infinity : b[1].sort;

    if (sortA === sortB) {
      // Если sort одинаковый, сортируем по name
      return a[1].name.localeCompare(b[1].name);
    }
    return sortA - sortB; // Сравниваем sort
  })
  .map(([key, value]) => ({ 
    ...value,
  }));
}

// сортировка сотрудников
const sortByEmployees = (data) => {
  data.forEach(item => {
    item.employees = sort(item.employees);
  });
  return data;
}


const calc = (data, calendar) => {
  data.forEach(department => {    
    department.employees.forEach(employee => {
      totalEmployees++;

      let index = 1;
      let previousDate = null;

      employee.vacations.forEach(vacation => {
        let dateFrom = moment(vacation.date_from);
        let dateTo = moment(vacation.date_to);

        if (previousDate == null || (previousDate.format('YYYY-MM-DD') != dateFrom.format('YYYY-MM-DD'))) {          
          index = 1;
        }
        previousDate = moment(dateTo);
        previousDate.add(1, 'day');

        while (dateFrom <= dateTo) {
          const month = dateFrom.format('MM');
          const day = +dateFrom.format('DD');         

          // информация по отделу
          if (!department.months[month]) {
            department.months[month] = { 
              total: [],
              days: {}, 
            }
          }

          if (!department.months[month].days[day]) {
            department.months[month].days[day] = { total: [] }
          }

          if (department.months[month].total.indexOf(employee.id) === -1) {
            department.months[month].total.push(employee.id);
          }          

          if (department.months[month].days[day].total.indexOf(employee.id) === -1) {
            department.months[month].days[day].total.push(employee.id);
          }

          // информация для сотрудника
          if (!employee.months[month]) {
            employee.months[month] = { total: [], days: {} };
          }
          employee.months[month]['days'][day] = {
            index: index,
            vacation: {
              kind: vacation.id_kind,
              name: vacation.name,
              status: vacation.status,
            },
          }
          if (employee.months[month].total.indexOf(day) === -1) {
            employee.months[month].total.push(day)
          }

          index++;
          dateFrom.add(1, 'days');
        }
      })
    })

    Object.keys(department.months).forEach(key => {
      calendar[+key].total += department.months[key].total.length;      
    })
  })
  return data;
}
