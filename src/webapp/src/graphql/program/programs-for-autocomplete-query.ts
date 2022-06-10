import gql from 'graphql-tag'

export const PROGRAMS_FOR_AUTOCOMPLETE = gql`
    query programsForAutocomplete ($search: String, $offset: Int, $limit: Int) {
        programsForAutocomplete (search: $search) {
            items (offset: $offset, limit: $limit) {
                id,
                name,
                description,
                status,
                type,
                dateStart,
                dateEnd,
                createdAt,
                updatedAt,
            },
            count,
        }
    }
`;
