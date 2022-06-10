import gql from 'graphql-tag'

export const PROGRAM_INDIVIDUAL_FRAGMENT = gql`
    fragment ProgramIndividualFragment on ProgramCoachingIndividual {
        id,
        firstName,
        lastName,
        email,
        phone,
        name,
        description,
        status,
        type,
        dateStart,
        dateEnd,
        period,
        programModel {
            id,
            name,
            description,
        },
        coaches {
            ...PartialUserFragment
        },
        createdAt,
        createdBy {
            ...PartialUserFragment
        },
        updatedAt,
        updatedBy {
            ...PartialUserFragment
        },
    }
    fragment PartialUserFragment on User {
        id,
        firstName,
        lastName,
        profilePicture {
            id
        },
    }
`;
