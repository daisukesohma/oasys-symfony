import gql from 'graphql-tag'
import {USER_FRAGMENT} from './user-fragment';

export const USER_BY_ID = gql`
    query userById ($id: String!, $fetchOnlyAttendingEvents: Boolean) {
        userById (id: $id) {
            programsByProgramsUsers {
                id,
                name,
                status,
                type,
                dateStart,
                dateEnd,
                coaches {
                    id,
                    firstName,
                    lastName,
                },
                events {
                    count,
                },
                eventsOrderedByDate (userId: $id, fetchOnlyAttending: $fetchOnlyAttendingEvents) {
                    count,
                    items {
                        id,
                        dateEvent,
                        dateEventEnd,
                        name,
                        status,
                        organizer {
                            id,
                            firstName,
                            lastName
                        }
                    }
                }, 
            },
            ...UserFragment
        }
    }
    ${USER_FRAGMENT}
`;
