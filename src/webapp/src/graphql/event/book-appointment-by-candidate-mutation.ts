import gql from 'graphql-tag'
import {EVENT_FRAGMENT} from './event-fragment';

export const BOOK_APPOINTMENT_BY_CANDIDATE = gql`
    mutation bookAppointmentByCandidate (
        $eventId: String!,
        $userId: String!
    ) {
        bookAppointmentByCandidate (
            eventId: $eventId,
            userId: $userId
        ) {
            ...EventFragment
        }
    }
    ${EVENT_FRAGMENT}
`;
