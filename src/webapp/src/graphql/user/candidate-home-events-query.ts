import gql from 'graphql-tag'
import {DOCUMENT_FRAGMENT} from "@/graphql/document/document-fragment";
import {CANDIDATE_EVENT_FRAGMENT} from "@/graphql/user/candidate-home-query";

export const CANDIDATE_HOME_EVENTS = gql`
    query candidateHomeEvents ($id: String!, $programId: String!, $fetchOnlyAttendingEvents: Boolean, $dateStart: String, $dateEnd: String) {
        programById (programId: $programId) {
            id
            type
            eventsOrderedByDate (fetchOnlyAttending: $fetchOnlyAttendingEvents, userId: $id, dateStart: $dateStart, dateEnd: $dateEnd) {
                items {
                    ...CandidateEventFragment,
                },
            },
            ...on ProgramPic {
                appointmentTimeLimit
            }
        }
    }
    ${CANDIDATE_EVENT_FRAGMENT}
`;
