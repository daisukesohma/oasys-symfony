import moment from 'moment';

/**
 * Since we do not store timezones in Database, we store the date in UTC
 * and then convert it to the local timezone for the user in the frontend
 *
 * The date ALSO NEEDS TO BE CONVERTED TO UTC when being submitted to
 * the server using a form using moment.utc(date).
 */
export default date =>
    moment(typeof date === 'undefined' ?
        date : (typeof date === "object" || date.indexOf('+') === -1 ? date :
            date.substr(0, date.indexOf('+'))));