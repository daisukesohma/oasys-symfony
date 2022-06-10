import gql from 'graphql-tag'

export const PROGRAM_FRAGMENT = gql`
    fragment ProgramFragment on ProgramInterface {
        id,
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
        usersHaveBeenInvited,
        company {
            id,
            name
        }
        endSupportEmail
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
