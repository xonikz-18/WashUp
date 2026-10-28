import {
  test,
  expect,
  type Page,
} from '@playwright/test';

const FRONTEND_URL = 'http://localhost:5173';
const BACKEND_URL = 'http://127.0.0.1:8000';

const ADMIN_USERNAME = 'admin';
const ADMIN_PASSWORD = 'Admin123!';

const TEST_ORDER_NUMBER = 'WU-0019';


// Types

type AuthResponse = {
  token: string;
  user: Record<string, unknown>;
};


// Helpers

async function loginAsAdmin(page: Page) {
  const response = await page.request.post(
    `${BACKEND_URL}/api/login`,
    {
      data: {
        username: ADMIN_USERNAME,
        password: ADMIN_PASSWORD,
      },
    }
  );

  expect(
    response.ok(),
    'Admin login API should return a successful response.'
  ).toBeTruthy();

  const data = (await response.json()) as AuthResponse;

  expect(data.token).toBeTruthy();

  await page.goto(FRONTEND_URL, {
    waitUntil: 'domcontentloaded',
  });

  await page.evaluate(
    (auth: AuthResponse) => {
      localStorage.setItem(
        'washup_token',
        auth.token
      );

      localStorage.setItem(
        'washup_user',
        JSON.stringify(auth.user)
      );
    },
    data
  );

  await page.reload({
    waitUntil: 'domcontentloaded',
  });

  await expect(
    page.getByText('ORDER SUMMARY', {
      exact: true,
    })
  ).toBeVisible({
    timeout: 10000,
  });
}


async function openCustomerTracker(page: Page) {
  await page.goto(
    `${FRONTEND_URL}/track-order`,
    {
      waitUntil: 'domcontentloaded',
    }
  );

  await expect(page).toHaveURL(
    /\/track-order\/?$/
  );

  await expect(
    page.locator('.customer-page')
  ).toBeVisible({
    timeout: 10000,
  });

  await expect(
    page.getByPlaceholder(
      'Enter your order number'
    )
  ).toBeVisible({
    timeout: 10000,
  });

  await expect(
  page.locator('.track-button')
).toBeVisible({
  timeout: 10000,
});
}


async function trackOrder(
  page: Page,
  orderNumber: string
) {
  await openCustomerTracker(page);

  const responsePromise =
    page.waitForResponse(
      response =>
        response.url().includes(
          '/api/track-order/'
        )
    );

  await page
    .getByPlaceholder(
      'Enter your order number'
    )
    .fill(orderNumber);

  await page.locator('.track-button').click();

  return responsePromise;
}


// 10 Functional Checks

test.describe(
  'FUNCTIONAL CHECKS - 10',
  () => {

    // 1. Admin login

    test(
      'Functional 01 - admin can log in successfully',
      async ({ page }) => {

        await page.goto(
          FRONTEND_URL,
          {
            waitUntil: 'domcontentloaded',
          }
        );

        await page
          .locator('#username')
          .fill(ADMIN_USERNAME);

        await page
          .locator('#password')
          .fill(ADMIN_PASSWORD);

        await page.getByRole('button', {
          name: 'Login',
          exact: true,
        }).click();

        await expect(
          page.getByText(
            'ORDER SUMMARY',
            { exact: true }
          )
        ).toBeVisible({
          timeout: 10000,
        });
      }
    );


    // 2. Invalid login

    test(
      'Functional 02 - invalid login is rejected',
      async ({ page }) => {

        await page.goto(
          FRONTEND_URL,
          {
            waitUntil: 'domcontentloaded',
          }
        );

        await page
          .locator('#username')
          .fill('wrong-user');

        await page
          .locator('#password')
          .fill('wrong-password');

        await page.getByRole('button', {
          name: 'Login',
          exact: true,
        }).click();

        await expect(
          page.getByRole('alert')
        ).toContainText(
          'Invalid admin username or password.'
        );

        await expect(
          page.locator('#username')
        ).toHaveClass(/input-error/);

        await expect(
          page.locator('#password')
        ).toHaveClass(/input-error/);
      }
    );


    // 3. Dashboard loads

    test(
      'Functional 03 - dashboard loads after authentication',
      async ({ page }) => {

        await loginAsAdmin(page);

        await expect(
          page.getByText(
            'Total Orders',
            { exact: true }
          )
        ).toBeVisible();

        await expect(
          page.getByText(
            'Total Revenue',
            { exact: true }
          )
        ).toBeVisible();

        await expect(
          page.getByText(
            'Pending Orders',
            { exact: true }
          )
        ).toBeVisible();
      }
    );


    // 4. Order search

    test(
      'Functional 04 - admin can search for an order',
      async ({ page }) => {

        await loginAsAdmin(page);

        await page.getByRole('button', {
          name: 'Orders',
          exact: true,
        }).click();

        await expect(
          page.getByRole('heading', {
            name: 'Orders',
            exact: true,
          })
        ).toBeVisible();

        const searchBox =
          page.getByPlaceholder(
            'Search by order number or customer name'
          );

        await searchBox.fill(
          TEST_ORDER_NUMBER
        );

        const matchingRow =
          page
            .locator(
              'tbody tr.order-row'
            )
            .filter({
              hasText: TEST_ORDER_NUMBER,
            });

        await expect(
          matchingRow
        ).toHaveCount(1);

        await expect(
          matchingRow
        ).toContainText(
          TEST_ORDER_NUMBER
        );
      }
    );


    // 5. Status filter

    test(
      'Functional 05 - admin can filter orders by status',
      async ({ page }) => {

        await loginAsAdmin(page);

        await page.getByRole('button', {
          name: 'Orders',
          exact: true,
        }).click();

        await expect(
          page.getByRole('heading', {
            name: 'Orders',
            exact: true,
          })
        ).toBeVisible();

        const statusFilter =
          page.locator('.status-filter');

        await statusFilter.selectOption({
          label: 'Pending',
        });

        const visibleRows =
          page.locator(
            'tbody tr.order-row'
          );

        await expect(
          visibleRows.first()
        ).toBeVisible();

        const count =
          await visibleRows.count();

        expect(count).toBeGreaterThan(0);

        for (let i = 0; i < count; i++) {

          await expect(
            visibleRows.nth(i)
          ).toContainText(
            'Pending'
          );
        }
      }
    );


    // 6. Order details

    test(
      'Functional 06 - admin can open order details',
      async ({ page }) => {

        await loginAsAdmin(page);

        await page.getByRole('button', {
          name: 'Orders',
          exact: true,
        }).click();

        await expect(
          page.getByRole('heading', {
            name: 'Orders',
            exact: true,
          })
        ).toBeVisible();

        const orderRow =
          page
            .locator(
              'tbody tr.order-row'
            )
            .filter({
              hasText: TEST_ORDER_NUMBER,
            });

        await expect(
          orderRow
        ).toHaveCount(1);

        await orderRow.click();

        await expect(
          page.getByText(
            'Order Information',
            { exact: true }
          )
        ).toBeVisible();

        await expect(
          page.getByText(
            'Order Details',
            { exact: true }
          )
        ).toBeVisible();
      }
    );


    // 7. Reports navigation

    test(
      'Functional 07 - admin can open Reports',
      async ({ page }) => {

        await loginAsAdmin(page);

        await page.getByRole('button', {
          name: 'Reports',
          exact: true,
        }).click();

        await expect(
          page.getByRole('heading', {
            name: 'Reports',
            exact: true,
          })
        ).toBeVisible({
          timeout: 10000,
        });

        await expect(
          page.getByText(
            'Orders & Sales',
            { exact: true }
          )
        ).toBeVisible();
      }
    );


    // 8. Customer tracking - valid order

    test(
      'Functional 08 - customer can track an existing order',
      async ({ page }) => {

        const responsePromise =
          await trackOrder(
            page,
            TEST_ORDER_NUMBER
          );

        expect(
          responsePromise.status()
        ).toBe(200);

        await expect(
          page.getByText(
            `Order ${TEST_ORDER_NUMBER}`,
            { exact: true }
          )
        ).toBeVisible({
          timeout: 10000,
        });

        await expect(
          page.getByText(
            'Pending',
            { exact: true }
          ).last()
        ).toBeVisible();
      }
    );


    // 9. Customer tracking - invalid order

    test(
      'Functional 09 - invalid order number shows not found state',
      async ({ page }) => {

        const responsePromise =
          await trackOrder(
            page,
            'WU-999999'
          );

        expect(
          responsePromise.status()
        ).toBe(404);

        await expect(
          page.getByText(
            'Order Not Found',
            { exact: true }
          )
        ).toBeVisible({
          timeout: 10000,
        });

        await expect(
          page.getByText(
            "We couldn't find an order with that Order Number."
          )
        ).toBeVisible();
      }
    );


    // 10. Duplicate feedback API

    test(
      'Functional 10 - duplicate feedback is rejected by the API',
      async ({ request }) => {

        const response =
          await request.post(
            `${BACKEND_URL}/api/feedbacks`,
            {
              data: {
                order_number:
                  TEST_ORDER_NUMBER,

                rating: 5,

                type: 'Feedback',

                comment:
                  'Automated duplicate feedback test.',
              },
            }
          );

        expect(
          response.status()
        ).toBe(409);

        const body =
          await response.json();

        expect(
          body.message
        ).toBe(
          'This order already has feedback submitted.'
        );
      }
    );

  }
);


// 10 UI Validation Checks

test.describe(
  'UI VALIDATION CHECKS - 10',
  () => {

    // 1. Login UI

    test(
      'UI 01 - login page renders correctly',
      async ({ page }) => {

        await page.goto(
          FRONTEND_URL,
          {
            waitUntil: 'domcontentloaded',
          }
        );

        await expect(
          page.getByAltText(
            'WashUP Logo'
          )
        ).toBeVisible();

        await expect(
          page.getByRole('heading', {
            name: 'Welcome Back',
            exact: true,
          })
        ).toBeVisible();

        const loginCard =
          page.locator('.login-card');

        await expect(
          loginCard
        ).toBeVisible();

        await expect(
          loginCard
        ).toHaveCSS(
          'border-top-width',
          '3px'
        );

        await expect(
          page.getByRole('button', {
            name: 'Login',
            exact: true,
          })
        ).toBeVisible();
      }
    );


    // 2. Password toggle

    test(
      'UI 02 - password visibility toggle changes field state',
      async ({ page }) => {

        await page.goto(
          FRONTEND_URL,
          {
            waitUntil: 'domcontentloaded',
          }
        );

        const passwordInput =
          page.locator('#password');

        await expect(
          passwordInput
        ).toHaveAttribute(
          'type',
          'password'
        );

        await page.getByRole('button', {
          name: 'Show password',
        }).click();

        await expect(
          passwordInput
        ).toHaveAttribute(
          'type',
          'text'
        );

        await expect(
          page.getByRole('button', {
            name: 'Hide password',
          })
        ).toBeVisible();
      }
    );


    // 3. Dashboard UI

    test(
      'UI 03 - dashboard cards and sidebar render correctly',
      async ({ page }) => {

        await loginAsAdmin(page);

        await expect(
          page.locator('.sidebar')
        ).toBeVisible();

        await expect(
          page.locator('.summary-card')
        ).toHaveCount(4);

        await expect(
          page.getByText(
            'ORDER SUMMARY',
            { exact: true }
          )
        ).toBeVisible();

        await expect(
          page.getByText(
            'Total Orders',
            { exact: true }
          )
        ).toBeVisible();

        await expect(
          page.getByText(
            'Total Revenue',
            { exact: true }
          )
        ).toBeVisible();
      }
    );


    // 4. Orders page UI

    test(
      'UI 04 - orders page controls and table render correctly',
      async ({ page }) => {

        await loginAsAdmin(page);

        await page.getByRole('button', {
          name: 'Orders',
          exact: true,
        }).click();

        await expect(
          page.getByRole('heading', {
            name: 'Orders',
            exact: true,
          })
        ).toBeVisible();

        await expect(
  page.locator('.add-order-button')
).toBeVisible();

        await expect(
          page.getByPlaceholder(
            'Search by order number or customer name'
          )
        ).toBeVisible();

        await expect(
          page.locator('.status-filter')
        ).toBeVisible();

        await expect(
          page.locator('.orders-card')
        ).toBeVisible();

        await expect(
          page.locator('table')
        ).toBeVisible();
      }
    );


    // 5. Status badge UI

    test(
      'UI 05 - order status badge renders with correct styling',
      async ({ page }) => {

        await loginAsAdmin(page);

        await page.getByRole('button', {
          name: 'Orders',
          exact: true,
        }).click();

        const pendingBadge =
          page.locator(
            '.status-badge.pending'
          ).first();

        await expect(
          pendingBadge
        ).toBeVisible();

        await expect(
          pendingBadge
        ).toHaveCSS(
          'border-radius',
          '20px'
        );

        await expect(
          pendingBadge.locator(
            '.status-dot'
          )
        ).toBeVisible();
      }
    );


    // 6. Order details UI

    test(
      'UI 06 - order details information cards render correctly',
      async ({ page }) => {

        await loginAsAdmin(page);

        await page.getByRole('button', {
          name: 'Orders',
          exact: true,
        }).click();

        const orderRow =
          page
            .locator(
              'tbody tr.order-row'
            )
            .filter({
              hasText: TEST_ORDER_NUMBER,
            });

        await expect(
          orderRow
        ).toHaveCount(1);

        await orderRow.click();

        await expect(
          page.getByText(
            'Order Information',
            { exact: true }
          )
        ).toBeVisible();

        await expect(
          page.getByText(
            'Order Details',
            { exact: true }
          )
        ).toBeVisible();

        await expect(
          page.getByText(
            'Add-ons',
            { exact: true }
          )
        ).toBeVisible();

        await expect(
          page.locator('.total-bar')
        ).toBeVisible();
      }
    );


    // 7. Services & Add-ons UI

    test(
      'UI 07 - services and add-ons page renders correctly',
      async ({ page }) => {

        await loginAsAdmin(page);

        await page.getByRole('button', {
          name: 'Services & Add-ons',
          exact: true,
        }).click();

        await expect(
          page.getByRole('heading', {
            name: 'Services & Add-ons',
            exact: true,
          })
        ).toBeVisible();

        await expect(
          page.getByText(
            'Services',
            { exact: true }
          )
        ).toBeVisible();

        await expect(
          page.getByText(
            'Add-ons',
            { exact: true }
          )
        ).toBeVisible();
      }
    );


    // 8. Reports UI

    test(
  'UI 08 - reports page renders tabs, summaries, and charts',
  async ({ page }) => {

    await loginAsAdmin(page);

    await page.getByRole('button', {
      name: 'Reports',
      exact: true,
    }).click();

    await expect(
      page.getByRole('heading', {
        name: 'Reports',
        exact: true,
      })
    ).toBeVisible({
      timeout: 10000,
    });

    await expect(
      page.getByText(
        'Orders & Sales',
        { exact: true }
      )
    ).toBeVisible();

    await expect(
      page.getByText(
        'Customer Feedback',
        { exact: true }
      )
    ).toBeVisible();

    await page.getByRole('button', {
      name: 'Orders & Sales',
      exact: true,
    }).click();

    await expect(
      page.locator('.chart-card').first()
    ).toBeVisible({
      timeout: 10000,
    });

    await expect(
      page.locator('.chart-container').first()
    ).toBeVisible({
      timeout: 10000,
    });
  }
);


    // 9. Customer tracker UI

    test(
      'UI 09 - customer tracker result and feedback state render correctly',
      async ({ page }) => {

        const responsePromise =
          await trackOrder(
            page,
            TEST_ORDER_NUMBER
          );

        expect(
          responsePromise.status()
        ).toBe(200);

        await expect(
          page.getByText(
            `Order ${TEST_ORDER_NUMBER}`,
            { exact: true }
          )
        ).toBeVisible({
          timeout: 10000,
        });

        const feedbackButton =
  page.locator('.feedback-button');

await expect(
  feedbackButton
).toBeVisible();

await expect(
  feedbackButton
).toContainText(
  'Feedback Submitted'
);

await expect(
  feedbackButton
).toBeDisabled();
      }
    );


    // 10. Responsive customer tracker

    test(
      'UI 10 - customer tracker remains usable on mobile viewport',
      async ({ page }) => {

        await page.setViewportSize({
          width: 390,
          height: 844,
        });

        await openCustomerTracker(page);

        await expect(
          page.getByAltText(
            'WashUP Logo'
          )
        ).toBeVisible();

        await expect(
          page.getByPlaceholder(
            'Enter your order number'
          )
        ).toBeVisible();

        await expect(
  page.locator('.track-button')
).toBeVisible({
  timeout: 10000,
});

        const hasHorizontalOverflow =
          await page.evaluate(() => {
            return (
              document.documentElement.scrollWidth >
              window.innerWidth
            );
          });

        expect(
          hasHorizontalOverflow
        ).toBe(false);
      }
    );

  }
);