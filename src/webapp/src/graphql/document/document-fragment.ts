import gql from 'graphql-tag'

export const DOCUMENT_FRAGMENT = gql`
    fragment DocumentFragment on Document {
        id,
        name,
        tags,
        description,
        elaborationDate,
        fileDescriptor {
            id,
            name
        }
        visibility,
        createdAt,
        toBeSigned,
        documentsSignersForUser {
            statusSignature
            memberId
        },
        author{
            id,
            profilePicture {
                id
            },
            firstName,
            lastName,
        },
        type,
        articleLink,
        toBeDisplayedInHomePage,
        programs {
            id
            name
        }
        events {
            program {
                name
            }
        }
        category {
            id
            label
        }
        livrable {
            id
            name
        }
    }
`;
