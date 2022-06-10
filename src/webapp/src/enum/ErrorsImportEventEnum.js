import localMoment from "../utils/localMoment";

export const EVENT_EXISTS = 'event_exists';
export const EVENT_MISSING_MEETING = 'event_missing_meeting';
export const FILE_INVALID = 'files_invalid';


export const ERRORS_IMPORT_EVENT = [
    {
        value: EVENT_EXISTS,
        message: (variable) => {
            return "Un événement " + variable['eventName'] + " commençant le " +
                localMoment(variable['eventDate'].date).format("DD/MM/YYYY") + " à " +
                localMoment(variable['eventDate'].date).format("HH:mm") +
                " et se terminant le " +
                localMoment(variable['eventDateEnd'].date).format("DD/MM/YYYY") + " à " +
                localMoment(variable['eventDateEnd'].date).format("HH:mm")
                + " pose conflit";
        }
    },
    {
        value: EVENT_MISSING_MEETING,
        message: (variable) => {
            return "L'événement " + variable['eventName'] + " manque le lien des équipes Microsoft";
        }
    },
    {
        value: FILE_INVALID,
        message: (variable) => {
            return "Le fichier n'est pas valide, veuillez vérifier l'encodage";
        }
    },
];