import gql from 'graphql-tag'

export const ALL_CANDIDATES = gql`
    query allCandidates ($email: String, $lastName: String, $firstName: String, $eventStatuses: [String!], $eventType: String, $programType: String, $date: String, $offset: Int, $limit: Int) {
        allCandidates (eventType: $eventType, eventStatuses: $eventStatuses, programType: $programType, email: $email, lastName: $lastName, firstName: $firstName, date: $date) {
            items (limit: $limit, offset: $offset) {
                user {
                    id,
                    firstName,
                    lastName,
                    profilePicture {
                        id,
                        name,
                        size,
                    },
                    company {
                        id,
                        name,
                    }
                },
                program {
                    id,
                    name,
                    status,
                    todos {
                        items {
                            user {
                                id
                            }
                        }
                    },
                },
                eventsCount,
                completedEventsCount,
                nextEvent {
                    id,
                    dateEvent,
                    dateEventEnd,
                    name,
                    status,
                    type,
                    teamsLink,
                    meetingPlace
                },
            },
            count
        }
    }
`;
