import gql from 'graphql-tag'

export const ALL_EVENTS = gql`
    query allEvents ($search: String, $status: String, $user: String, $startDate: String, $endDate: String, $sortColumn: String, $sortDirection: String, $organizer: String, $offset: Int, $limit: Int) {
        allEvents (search: $search, status: $status, organizer: $organizer, user: $user, startDate: $startDate, endDate: $endDate, sortColumn: $sortColumn, sortDirection: $sortDirection) {
            items (offset: $offset, limit: $limit) {
                id,
                name,
                description,
                type,
                status,
                program {
                    id,
                    name,
                    description,
                    type,
                },
                users {
                    id,
                    firstName,
                    lastName,
                },
                dateEvent,
                dateEventEnd,
                createdAt,
                updatedAt,
            },
            count,
        }
    }
`;
