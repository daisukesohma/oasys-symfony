import gql from 'graphql-tag'

export const ALL_PROFESSIONAL_CATEGORIES = gql`
    query allProfessionalCategories {
        allProfessionalCategories {
            items {
                id
                label
            }
        }
    }
`;
