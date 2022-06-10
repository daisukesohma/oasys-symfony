import gql from 'graphql-tag'

export const ALL_PROGRAMS = gql`
    query allPrograms ($search: String, $status: String, $sortColumn: String, $sortDirection: String, $candidateId: String, $offset: Int, $limit: Int) {
        allPrograms (search: $search, status: $status, sortColumn: $sortColumn, sortDirection: $sortDirection, candidateId: $candidateId) {
            items (offset: $offset, limit: $limit) {
                id,
                name,
                description,
                status,
                type,
                dateStart,
                dateEnd,
                period
                programModel {
                    id,
                    name,
                    description,
                },
                users {
                    items (limit: 1) {
                        id,
                        firstName,
                        lastName,
                    }
                },
                createdAt,
                updatedAt,
            },
            count,
        }
    }
`;
