import gql from 'graphql-tag'

export const ALL_VILLES_FRANCE = gql`
    query allVillesFrance ($search: String!, $limit: Int!) {
        allVillesFrance (search: $search) {
            items (limit: $limit) {
                id,
                codePostal,
                departmentNumber,
                departmentName,
                commonName,
                regionName,
            },
            count,
        }
    }
`;
