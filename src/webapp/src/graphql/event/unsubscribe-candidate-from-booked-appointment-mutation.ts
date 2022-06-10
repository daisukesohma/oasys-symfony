import gql from 'graphql-tag'
import {EVENT_FRAGMENT} from './event-fragment';

export const UNSUBSCRIBE_CANDIDATE_FROM_BOOKED_APPOINTMENT = gql`
    mutation unsubscribeCandidateFromBookedAppointment (
        $eventId: String!,
        $userId: String!
    ) {
        unsubscribeCandidateFromBookedAppointment (
            eventId: $eventId,
            userId: $userId
        ) {
            ...EventFragment
        }
    }
    ${EVENT_FRAGMENT}
`;
